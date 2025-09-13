<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentWayLog extends Model
{
    protected $table = "payment_way_logs";

    protected $fillable = [
        'payment_way_id',
        'created_by',
        'action', //create, update, delete
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];


    public function paymentWay()
    {
        return $this->belongsTo(PaymentWay::class, 'payment_way_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
