<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWay extends Model
{
    protected $table = 'payment_ways';

    protected $fillable = [
        'name',
        'type',     // cash, wallet, balance_machine
        'phone_number',
        'send_limit',
        'send_limit_alert',
        'receive_limit',
        'receive_limit_alert',
        'balance',
        'created_by',
        'position',
        'client_type'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'payment_way_id')->latest();
    }

    public function logs()
    {
        return $this->hasMany(PaymentWayLog::class, 'payment_way_id')->latest();
    }

    public function monthlyLimits()
    {
        return $this->hasMany(PaymentWayLimit::class, 'payment_way_id');
    }
}
