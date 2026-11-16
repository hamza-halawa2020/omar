<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Umrah\Booking;
use App\Models\Umrah\Document;
use App\Models\Umrah\Notification;
use App\Models\Umrah\Payment;
use App\Models\Umrah\Program;
use App\Models\Umrah\Traveler;
use App\Models\Umrah\Trip;
use App\Services\PhoneNumberService;
use App\Services\UmrahNotificationService;
use App\Services\UmrahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UmrahController extends Controller
{
    public function __construct(private readonly UmrahService $service) {}

    private function money(bool $positive = true, bool $required = true): array
    {
        return [$required ? 'required' : 'nullable', 'regex:/^[0-9]{1,10}(\.[0-9]{1,2})?$/D', 'numeric', $positive ? 'gt:0' : 'min:0'];
    }

    private function saved()
    {
        return back()->with('success', __('umrah.saved'));
    }

    public function index()
    {
        $trips = Trip::withCount('bookings')->orderByDesc('departure_date')->paginate(20);
        $metrics = [
            __('umrah.metric_open_trips') => Trip::where('status', 'open')->whereDate('departure_date', '>=', today())->count(),
            __('umrah.metric_active_bookings') => Booking::where('status', 'confirmed')->count(),
            __('umrah.metric_pending_payments') => Payment::where('status', 'pending')->count(),
            __('umrah.metric_pending_visas') => Booking::where('status', 'confirmed')->whereIn('visa_status', ['not_started', 'processing'])->count(),
        ];

        return view('dashboard.umrah.index', compact('trips', 'metrics'));
    }

    public function saveTrip(Request $request, ?int $id = null)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'departure_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'capacity' => ['required', 'integer', 'min:1', 'max:100000'],
            'status' => ['required', Rule::in(config('umrah.trip_statuses'))],
            'transport' => ['nullable', 'string', 'max:255'], 'notes' => ['nullable', 'string', 'max:5000'],
        ]);
        $trip = DB::transaction(function () use ($id, $data) {
            if (! $id) {
                return Trip::create($data + ['created_by' => auth('web')->id()]);
            }
            $trip = Trip::whereKey($id)->lockForUpdate()->firstOrFail();
            $active = $trip->bookings()->where('status', '!=', 'cancelled')->count();
            if ($data['capacity'] < $active || ($data['status'] === 'cancelled' && $active > 0)) {
                throw ValidationException::withMessages(['capacity' => __('umrah.error_trip_capacity')]);
            }
            $before = $trip->only(['name', 'departure_date', 'return_date', 'transport', 'status']);
            $trip->update($data);
            if ($trip->wasChanged(array_keys($before))) {
                foreach ($trip->bookings()->where('status', '!=', 'cancelled')->with('traveler')->get() as $booking) {
                    app(UmrahNotificationService::class)->record($booking->traveler, 'trip_updated', 'trip-update:'.$trip->id.':'.$booking->id.':'.Str::uuid(),
                        'تم تحديث رحلتك '.$trip->name.'. المغادرة: '.$trip->departure_date->toDateString().'، العودة: '.$trip->return_date->toDateString().'، وسيلة السفر: '.$trip->transport.'، الحالة: '.__('umrah.'.$trip->status));
                }
            }

            return $trip;
        });

        return redirect()->route('umrah.trips.show', $trip->id)->with('success', __('umrah.trip_saved'));
    }

    public function trip(int $id)
    {
        $trip = Trip::with(['programs.prices'])->findOrFail($id);
        $bookings = $trip->bookings()->with(['traveler', 'payments', 'schedules'])->latest()->paginate(50);
        $travelers = Traveler::orderBy('name')->get(['id', 'name', 'phone']);

        return view('dashboard.umrah.trip', compact('trip', 'bookings', 'travelers'));
    }

    public function saveProgram(Request $request, int $tripId, ?int $id = null)
    {
        $trip = Trip::findOrFail($tripId);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'makkah_hotel' => ['nullable', 'string', 'max:255'],
            'madinah_hotel' => ['nullable', 'string', 'max:255'], 'makkah_nights' => ['required', 'integer', 'between:0,365'],
            'madinah_nights' => ['required', 'integer', 'between:0,365'], 'services' => ['nullable', 'string', 'max:5000'],
        ]);
        if ($id) {
            $trip->programs()->findOrFail($id)->update($data);
        } else {
            $trip->programs()->create($data);
        }

        return $this->saved();
    }

    public function savePrice(Request $request, int $programId, ?int $id = null)
    {
        $program = Program::findOrFail($programId);
        $data = $request->validate(['label' => ['required', 'string', 'max:255'], 'amount' => $this->money(),
            'currency' => ['required', Rule::in(['EGP', 'SAR', 'USD'])], 'is_active' => ['required', 'boolean']]);
        if ($id) {
            $program->prices()->findOrFail($id)->update($data);
        } else {
            $program->prices()->create($data);
        }

        return $this->saved();
    }

    public function travelers(Request $request)
    {
        $search = $request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? '';
        $travelers = Traveler::when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%')))->latest()->paginate(30)->withQueryString();

        $countryCodeOptions = app(PhoneNumberService::class)->countryCodeOptions();

        return view('dashboard.umrah.travelers', compact('travelers', 'search', 'countryCodeOptions'));
    }

    public function saveTraveler(Request $request, ?int $id = null)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'], 'english_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required_without:phone_number', 'nullable', 'string', 'max:40'],
            'country_code' => ['required_with:phone_number', 'nullable', 'string', 'regex:/^\+[0-9]{1,4}$/'],
            'phone_number' => ['required_with:country_code', 'nullable', 'string', 'max:40'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'passport_number' => ['nullable', 'string', 'max:60', Rule::unique('umrah_travelers', 'passport_number')->ignore($id)],
            'passport_expires_at' => ['nullable', 'date'], 'notes' => ['nullable', 'string', 'max:5000'],
            'whatsapp_enabled' => ['required', 'boolean'],
        ]);
        if (isset($data['phone_number'])) {
            $data['phone'] = app(PhoneNumberService::class)->international($data['country_code'], $data['phone_number']);
        }
        unset($data['country_code'], $data['phone_number']);
        $traveler = DB::transaction(function () use ($id, $data) {
            $traveler = $id ? Traveler::whereKey($id)->lockForUpdate()->firstOrFail() : new Traveler;
            $traveler->fill($data);
            $changed = $traveler->isDirty();
            if (! $id) {
                $traveler->created_by = auth('web')->id();
            }
            $traveler->save();
            if ($changed) {
                app(UmrahNotificationService::class)->record($traveler, 'traveler_saved', 'traveler:'.$traveler->id.':'.Str::uuid(),
                    $id ? 'تم تحديث بيانات ملفك لدينا. للتأكد من البيانات أو تصحيحها تواصل مع المكتب.' : 'تم فتح ملفك لدينا لمتابعة حجز العمرة وإجراءات السفر.');
            }

            return $traveler;
        });

        return redirect()->route('umrah.travelers.show', $traveler->id)->with('success', __('umrah.traveler_saved_notice'));
    }

    public function traveler(int $id)
    {
        $traveler = Traveler::with(['bookings.trip', 'documents', 'movements'])->findOrFail($id);
        $notifications = Notification::where('traveler_id', $id)
            ->when(! auth()->user()->hasPermissionTo('umrah_passports_view'), fn ($q) => $q->whereNotIn('event', ['passport_movement', 'document_added']))
            ->latest()->paginate(30);

        $countryCodeOptions = app(PhoneNumberService::class)->countryCodeOptions();

        return view('dashboard.umrah.traveler', compact('traveler', 'notifications', 'countryCodeOptions'));
    }

    public function createBooking(Request $request)
    {
        $booking = $this->service->createBooking($request->validate([
            'trip_id' => ['required', 'integer'], 'traveler_id' => ['required', 'integer'],
            'program_price_id' => ['required', 'integer'], 'notes' => ['nullable', 'string', 'max:5000'],
            'agreed_amount' => $this->money(false, false),
            'price_adjustment_reason' => ['nullable', 'string', 'max:2000'],
        ]));

        return redirect()->route('umrah.bookings.show', $booking->id)->with('success', __('umrah.booking_saved'));
    }

    public function booking(int $id)
    {
        $booking = Booking::with(['traveler', 'trip.programs.prices', 'payments', 'schedules', 'logs'])->findOrFail($id);

        return view('dashboard.umrah.booking', compact('booking'));
    }

    public function payment(Request $request, int $id)
    {
        $data = $request->validate([
            'amount' => $this->money(), 'submission_token' => ['required', 'uuid'],
            'type' => ['required', Rule::in(['collection', 'refund'])], 'status' => ['required', Rule::in(['pending', 'confirmed'])],
            'method' => ['required', 'string', 'max:60'], 'paid_at' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'external_reference' => ['nullable', 'string', 'max:255'], 'notes' => ['nullable', 'string', 'max:5000'],
        ]);
        if ($data['type'] === 'refund') {
            abort_unless($request->user()->hasPermissionTo('umrah_refunds_approve'), 403);
        } else {
            abort_unless($request->user()->hasPermissionTo('umrah_payments_collect'), 403);
            if ($data['status'] === 'confirmed') {
                abort_unless($request->user()->hasPermissionTo('umrah_payments_confirm'), 403);
            }
        }
        $this->service->addPayment($id, $data);

        return $this->saved();
    }

    public function reviewPayment(Request $request, int $id, int $paymentId)
    {
        $data = $request->validate(['confirm' => ['required', 'boolean']]);
        $this->service->reviewPayment($id, $paymentId, (bool) $data['confirm']);

        return $this->saved();
    }

    public function settle(Request $request, int $id)
    {
        $data = $request->validate(['total_amount' => $this->money(false), 'reason' => ['required', 'string', 'max:2000'], 'cancel' => ['required', 'boolean']]);
        $this->service->settle($id, $data, (bool) $data['cancel']);

        return $this->saved();
    }

    public function changeProgram(Request $request, int $id)
    {
        $this->service->changeProgram($id, $request->validate(['program_price_id' => ['required', 'integer'], 'reason' => ['required', 'string', 'max:2000']]));

        return $this->saved();
    }

    public function schedule(Request $request, int $id)
    {
        $this->service->addSchedule($id, $request->validate(['amount' => $this->money(), 'due_date' => ['required', 'date_format:Y-m-d']]));

        return $this->saved();
    }

    public function procedures(Request $request, int $id)
    {
        $this->service->procedures($id, $request->validate([
            'visa_status' => ['required', Rule::in(config('umrah.visa_statuses'))],
            'ticket_status' => ['required', Rule::in(config('umrah.ticket_statuses'))],
            'travel_status' => ['required', Rule::in(config('umrah.travel_statuses'))],
        ]));

        return $this->saved();
    }

    public function complete(int $id)
    {
        $this->service->complete($id);

        return $this->saved();
    }

    public function movement(Request $request, int $id)
    {
        $this->service->passportMovement($id, $request->validate([
            'location' => ['required', 'string', 'max:255'], 'recipient' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]));

        return $this->saved();
    }

    public function upload(Request $request, int $id)
    {
        $traveler = Traveler::findOrFail($id);
        $data = $request->validate(['label' => ['required', 'string', 'max:255'], 'document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240']]);
        $path = $request->file('document')->store('tenants/'.tenant('id'), 'umrah_private');
        try {
            DB::transaction(function () use ($traveler, $data, $path, $request) {
                $document = $traveler->documents()->create(['label' => $data['label'], 'path' => $path,
                    'original_name' => $request->file('document')->getClientOriginalName(), 'created_by' => auth('web')->id()]);
                app(UmrahNotificationService::class)->record($traveler, 'document_added', 'document:'.$document->id, 'تم استلام مستند في ملفك: '.$data['label']);
            });
        } catch (\Throwable $e) {
            Storage::disk('umrah_private')->delete($path);
            throw $e;
        }

        return $this->saved();
    }

    public function download(int $id)
    {
        $document = Document::findOrFail($id);
        abort_unless(str_starts_with($document->path, 'tenants/'.tenant('id').'/'), 404);

        return Storage::disk('umrah_private')->download($document->path, $document->original_name, ['X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store']);
    }

    public function receipt(int $id)
    {
        $payment = Payment::with('booking.traveler', 'booking.trip')->findOrFail($id);

        return view('dashboard.umrah.receipt', compact('payment'));
    }

    public function reports(Request $request)
    {
        $filters = $request->validate(['trip_id' => ['nullable', 'integer'], 'currency' => ['nullable', Rule::in(['EGP', 'SAR', 'USD'])],
            'from' => ['nullable', 'date'], 'to' => ['nullable', 'date', 'after_or_equal:from']]);
        $bookings = Booking::with(['traveler', 'trip', 'payments', 'schedules'])
            ->when($filters['trip_id'] ?? null, fn ($q, $id) => $q->where('trip_id', $id))
            ->when($filters['currency'] ?? null, fn ($q, $currency) => $q->where('currency', $currency))->latest()->get();
        $payments = Payment::with('booking.traveler')->where('status', 'confirmed')
            ->whereHas('booking', fn ($q) => $q->when($filters['trip_id'] ?? null, fn ($q, $id) => $q->where('trip_id', $id))->when($filters['currency'] ?? null, fn ($q, $currency) => $q->where('currency', $currency)))
            ->when($filters['from'] ?? null, fn ($q, $date) => $q->whereDate('paid_at', '>=', $date))
            ->when($filters['to'] ?? null, fn ($q, $date) => $q->whereDate('paid_at', '<=', $date))->latest()->get();
        $trips = Trip::orderByDesc('departure_date')->get(['id', 'name']);

        return view('dashboard.umrah.reports', compact('bookings', 'payments', 'trips', 'filters'));
    }

    public function retryNotification(int $id)
    {
        // Unknown outcomes are intentionally excluded: retrying may send a duplicate.
        $notification = Notification::findOrFail($id);
        if (in_array($notification->event, ['passport_movement', 'document_added'], true)) {
            abort_unless(auth()->user()->hasPermissionTo('umrah_passports_view'), 403);
        }
        abort_unless(in_array($notification->status, ['failed', 'skipped'], true), 422);
        abort_unless($notification->traveler->whatsapp_enabled, 422);
        $updated = Notification::whereKey($id)->whereIn('status', ['failed', 'skipped'])
            ->update(['status' => 'pending', 'created_by' => auth('web')->id()]);
        if ($updated) {
            app(UmrahNotificationService::class)->deliver($id);
        }

        return $this->saved();
    }
}
