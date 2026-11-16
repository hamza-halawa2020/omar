<?php

namespace App\Models\Umrah;

use App\Support\UmrahMoney;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'umrah_bookings';

    protected $guarded = ['id'];

    protected $casts = ['agreement' => 'array', 'total_amount' => 'decimal:2'];

    public function traveler()
    {
        return $this->belongsTo(Traveler::class);
    }

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function price()
    {
        return $this->belongsTo(ProgramPrice::class, 'program_price_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function schedules()
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function logs()
    {
        return $this->hasMany(BookingLog::class);
    }

    public function paidCents(): int
    {
        return $this->payments->where('status', 'confirmed')->sum(fn (Payment $payment) => UmrahMoney::cents($payment->amount) * ($payment->type === 'refund' ? -1 : 1)
        );
    }

    public function balanceCents(): int
    {
        return UmrahMoney::cents($this->total_amount) - $this->paidCents();
    }

    public function overdueCents(): int
    {
        if ($this->status === 'cancelled') {
            return 0;
        }

        $due = $this->schedules->filter(fn ($schedule) => $schedule->due_date->lt(today()))
            ->sum(fn ($schedule) => UmrahMoney::cents($schedule->amount));

        return max(0, min($this->balanceCents(), $due - $this->paidCents()));
    }
}
