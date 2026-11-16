<?php

namespace App\Models\Umrah;

use Illuminate\Database\Eloquent\Model;

class Traveler extends Model
{
    protected $table = 'umrah_travelers';

    protected $guarded = ['id'];

    protected $casts = ['birth_date' => 'date', 'passport_expires_at' => 'date', 'whatsapp_enabled' => 'boolean'];

    protected $hidden = ['passport_number'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function movements()
    {
        return $this->hasMany(PassportMovement::class);
    }
}
