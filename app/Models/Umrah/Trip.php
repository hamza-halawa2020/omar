<?php

namespace App\Models\Umrah;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $table = 'umrah_trips';

    protected $guarded = ['id'];

    protected $casts = ['departure_date' => 'date', 'return_date' => 'date'];

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
