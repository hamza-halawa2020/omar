<?php

namespace App\Models\Umrah;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'umrah_payments';

    protected $guarded = ['id'];

    protected $casts = ['amount' => 'decimal:2', 'paid_at' => 'date'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
