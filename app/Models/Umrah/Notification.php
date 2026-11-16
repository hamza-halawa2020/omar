<?php

namespace App\Models\Umrah;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'umrah_notifications';

    protected $guarded = ['id'];

    protected $casts = ['attempted_at' => 'datetime'];

    public function traveler()
    {
        return $this->belongsTo(Traveler::class);
    }
}
