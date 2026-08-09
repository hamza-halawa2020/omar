<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IphoneLog extends Model
{
    protected $table = 'iphone_logs';

    protected $fillable = [
        'iphone_id',
        'transaction_id',
        'payment_way_id',
        'client_id',
        'action_type',
        'amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function iphone()
    {
        return $this->belongsTo(Iphone::class, 'iphone_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function paymentWay()
    {
        return $this->belongsTo(PaymentWay::class, 'payment_way_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
