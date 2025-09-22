<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWayLimit extends Model
{
    protected $table = 'payment_way_limits';

    protected $fillable = [
        'payment_way_id',
        'month',
        'year',
        'send_limit',
        'send_used',
        'receive_limit',
        'receive_used',
    ];

    public function paymentWay()
    {
        return $this->belongsTo(PaymentWay::class, 'payment_way_id');
    }
}