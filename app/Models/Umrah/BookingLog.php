<?php

namespace App\Models\Umrah;

use Illuminate\Database\Eloquent\Model;

class BookingLog extends Model
{
    protected $table = 'umrah_booking_logs';

    protected $guarded = ['id'];

    protected $casts = ['data' => 'array'];
}
