<?php

namespace App\Services;

use App\Models\Umrah\Booking;
use App\Models\Umrah\Payment;
use App\Models\Umrah\ProgramPrice;
use App\Models\Umrah\Traveler;
use App\Models\Umrah\Trip;
use App\Support\UmrahMoney as Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UmrahService
{
    private function reject(bool $condition, string $message): void
    {
        if ($condition) {
            throw ValidationException::withMessages(['umrah' => $message]);
        }
    }

    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $trip = Trip::whereKey($data['trip_id'])->lockForUpdate()->firstOrFail();
            $this->reject($trip->status !== 'open', __('umrah.error_trip_closed'));
            $this->reject($trip->bookings()->where('status', '!=', 'cancelled')->count() >= $trip->capacity, __('umrah.error_trip_full'));
            $traveler = Traveler::findOrFail($data['traveler_id']);
            $this->reject($trip->bookings()->where('traveler_id', $traveler->id)->where('status', '!=', 'cancelled')->exists(), __('umrah.error_duplicate_booking'));
            $price = ProgramPrice::with('program')->whereKey($data['program_price_id'])->lockForUpdate()->firstOrFail();
            $this->reject(! $price->is_active || $price->program->trip_id !== $trip->id, __('umrah.error_price_trip'));
            $agreedAmount = Money::decimal(Money::cents((string) ($data['agreed_amount'] ?? $price->amount)));
            $priceAdjustmentReason = $data['price_adjustment_reason'] ?? null;
            $booking = Booking::create([
                'reference' => (string) Str::uuid(), 'traveler_id' => $traveler->id,
                'trip_id' => $trip->id, 'program_price_id' => $price->id,
                'agreement' => ['program' => $price->program->only(['name', 'makkah_hotel', 'madinah_hotel', 'makkah_nights', 'madinah_nights', 'services']), 'label' => $price->label, 'price' => $price->amount,
                    'agreed_amount' => $agreedAmount, 'price_adjustment_reason' => $priceAdjustmentReason],
                'total_amount' => $agreedAmount, 'currency' => $price->currency,
                'notes' => $data['notes'] ?? null, 'created_by' => auth('web')->id(),
            ]);
            $this->log($booking, 'booking_created', ['total' => $booking->total_amount,
                'listed_price' => $price->amount, 'agreed_amount' => $agreedAmount, 'price_adjustment_reason' => $priceAdjustmentReason]);

            return $booking;
        }, 3);
    }

    public function addPayment(int $bookingId, array $data): Payment
    {
        return DB::transaction(function () use ($bookingId, $data) {
            $booking = Booking::whereKey($bookingId)->lockForUpdate()->firstOrFail();
            $existing = Payment::where('submission_token', $data['submission_token'])->first();
            if ($existing) {
                $this->reject($existing->booking_id !== $bookingId
                    || $existing->type !== $data['type'] || $existing->amount !== Money::decimal(Money::cents((string) $data['amount']))
                    || $existing->method !== $data['method'] || $existing->paid_at->toDateString() !== $data['paid_at']
                    || (string) $existing->external_reference !== (string) ($data['external_reference'] ?? '')
                    || (string) $existing->notes !== (string) ($data['notes'] ?? ''), __('umrah.error_duplicate_token'));

                return $existing;
            }
            $amount = Money::cents((string) $data['amount']);
            $this->reject($amount <= 0, __('umrah.error_positive_amount'));
            $refund = $data['type'] === 'refund';
            $status = $refund ? 'confirmed' : $data['status'];
            if (! $refund) {
                $this->reject($booking->status !== 'confirmed', __('umrah.error_inactive_collection'));
                $pending = $booking->payments()->where('status', 'pending')->get()->sum(fn ($p) => Money::cents($p->amount));
                $this->reject($amount > $booking->balanceCents() - $pending, __('umrah.error_overpayment'));
            } else {
                $this->reject($amount > max(0, -$booking->balanceCents()), __('umrah.error_excess_refund'));
            }
            $payment = $booking->payments()->create([
                'submission_token' => $data['submission_token'], 'receipt_number' => (string) Str::uuid(),
                'type' => $data['type'], 'status' => $status, 'amount' => Money::decimal($amount),
                'method' => $data['method'], 'external_reference' => $data['external_reference'] ?? null,
                'paid_at' => $data['paid_at'], 'notes' => $data['notes'] ?? null,
                'created_by' => auth('web')->id(), 'confirmed_by' => $status === 'confirmed' ? auth('web')->id() : null,
            ]);
            $booking->unsetRelation('payments');
            $this->log($booking, $refund ? 'refund_created' : 'payment_'.$status, ['payment_id' => $payment->id, 'receipt' => $payment->receipt_number, 'amount' => $payment->amount, 'balance' => Money::decimal($booking->balanceCents())]);

            return $payment;
        }, 3);
    }

    public function reviewPayment(int $bookingId, int $paymentId, bool $confirm): void
    {
        DB::transaction(function () use ($bookingId, $paymentId, $confirm) {
            $booking = Booking::whereKey($bookingId)->lockForUpdate()->firstOrFail();
            $payment = $booking->payments()->whereKey($paymentId)->lockForUpdate()->firstOrFail();
            $this->reject($payment->status !== 'pending', __('umrah.error_payment_reviewed'));
            if ($confirm) {
                $this->reject($booking->status === 'cancelled' || Money::cents($payment->amount) > $booking->balanceCents(), __('umrah.error_confirm_balance'));
            }
            $payment->update(['status' => $confirm ? 'confirmed' : 'void', 'confirmed_by' => auth('web')->id()]);
            $booking->unsetRelation('payments');
            $this->log($booking, $confirm ? 'payment_confirmed' : 'payment_void', ['payment_id' => $payment->id, 'receipt' => $payment->receipt_number, 'amount' => $payment->amount, 'balance' => Money::decimal($booking->balanceCents())]);
        }, 3);
    }

    public function settle(int $bookingId, array $data, bool $cancel): void
    {
        DB::transaction(function () use ($bookingId, $data, $cancel) {
            $booking = Booking::whereKey($bookingId)->lockForUpdate()->firstOrFail();
            $this->reject($booking->status !== 'confirmed', __('umrah.error_closed_adjustment'));
            $this->reject($booking->payments()->where('status', 'pending')->exists(), __('umrah.error_pending_agreement'));
            $before = $booking->total_amount;
            $schedulesBefore = $booking->schedules->map->only(['due_date', 'amount'])->all();
            $booking->update(['total_amount' => Money::decimal(Money::cents((string) $data['total_amount'])), 'status' => $cancel ? 'cancelled' : $booking->status]);
            $booking->schedules()->delete();
            $this->log($booking, $cancel ? 'booking_cancelled' : 'agreement_adjusted', ['before' => $before, 'after' => $booking->total_amount, 'reason' => $data['reason'], 'balance' => Money::decimal($booking->balanceCents()), 'schedule_reset' => true, 'previous_schedules' => $schedulesBefore]);
        }, 3);
    }

    public function changeProgram(int $bookingId, array $data): void
    {
        DB::transaction(function () use ($bookingId, $data) {
            $booking = Booking::whereKey($bookingId)->lockForUpdate()->firstOrFail();
            $this->reject($booking->status !== 'confirmed', __('umrah.error_inactive_booking'));
            $this->reject($booking->payments()->where('status', 'pending')->exists(), __('umrah.error_pending_payments'));
            $price = ProgramPrice::with('program')->whereKey($data['program_price_id'])->lockForUpdate()->firstOrFail();
            $this->reject(! $price->is_active || $price->program->trip_id !== $booking->trip_id || $price->currency !== $booking->currency, __('umrah.error_new_price'));
            $before = $booking->agreement;
            $schedulesBefore = $booking->schedules->map->only(['due_date', 'amount'])->all();
            $totalBefore = $booking->total_amount;
            $booking->update(['program_price_id' => $price->id, 'total_amount' => $price->amount,
                'agreement' => ['program' => $price->program->only(['name', 'makkah_hotel', 'madinah_hotel', 'makkah_nights', 'madinah_nights', 'services']), 'label' => $price->label, 'price' => $price->amount]]);
            $booking->schedules()->delete();
            $this->log($booking, 'program_changed', ['before' => $before, 'after' => $booking->agreement, 'previous_total' => $totalBefore, 'total' => $booking->total_amount, 'reason' => $data['reason'], 'schedule_reset' => true, 'previous_schedules' => $schedulesBefore]);
        }, 3);
    }

    public function addSchedule(int $bookingId, array $data): void
    {
        DB::transaction(function () use ($bookingId, $data) {
            $booking = Booking::whereKey($bookingId)->lockForUpdate()->firstOrFail();
            $scheduled = $booking->schedules->sum(fn ($s) => Money::cents($s->amount));
            $this->reject($booking->status !== 'confirmed' || $scheduled + Money::cents((string) $data['amount']) > Money::cents($booking->total_amount), __('umrah.error_schedule'));
            $schedule = $booking->schedules()->create($data);
            $this->log($booking, 'schedule_added', ['amount' => $schedule->amount, 'due_date' => $schedule->due_date->toDateString()]);
        }, 3);
    }

    public function procedures(int $bookingId, array $data): void
    {
        DB::transaction(function () use ($bookingId, $data) {
            $booking = Booking::whereKey($bookingId)->lockForUpdate()->firstOrFail();
            $this->reject($booking->status !== 'confirmed', __('umrah.error_inactive_booking'));
            $before = $booking->only(array_keys($data));
            $booking->update($data);
            if ($before !== $booking->only(array_keys($data))) {
                $this->log($booking, 'procedures_updated', ['before' => $before, 'after' => $booking->only(array_keys($data))]);
            }
        }, 3);
    }

    public function passportMovement(int $travelerId, array $data): void
    {
        DB::transaction(function () use ($travelerId, $data) {
            $traveler = Traveler::whereKey($travelerId)->lockForUpdate()->firstOrFail();
            $movement = $traveler->movements()->create($data + ['created_by' => auth('web')->id()]);
            $traveler->update(['passport_location' => $data['location']]);
            app(UmrahNotificationService::class)->record($traveler, 'passport_movement', 'passport:'.$movement->id,
                'تم تسجيل حركة جواز سفرك: '.$data['location'].'، المستلم: '.$data['recipient']);
        }, 3);
    }

    public function complete(int $bookingId): void
    {
        DB::transaction(function () use ($bookingId) {
            $booking = Booking::whereKey($bookingId)->lockForUpdate()->firstOrFail();
            $this->reject($booking->status !== 'confirmed' || $booking->travel_status !== 'returned'
                || $booking->balanceCents() !== 0 || $booking->payments()->where('status', 'pending')->exists(),
                __('umrah.error_close_booking'));
            $booking->update(['status' => 'completed']);
            $this->log($booking, 'booking_completed', ['balance' => '0.00']);
        }, 3);
    }

    private function log(Booking $booking, string $action, array $data): void
    {
        $log = $booking->logs()->create(['action' => $action, 'data' => $data, 'created_by' => auth('web')->id()]);
        app(UmrahNotificationService::class)->bookingEvent($booking, $log);
    }
}
