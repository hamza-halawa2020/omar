<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{


    protected $table = "transactions";

    protected $fillable = [
        'payment_way_id',
        'created_by',
        'type',     //send, receive
        'amount',
        'commission',
        'notes',
        'attachment',
        'client_id',
    ];

   

    public function paymentWay()
    {
        return $this->belongsTo(PaymentWay::class, 'payment_way_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(TransactionLog::class, 'transaction_id')->latest();
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function installmentPayment()
    {
        return $this->hasMany(InstallmentPayment::class, 'transaction_id');
    }
}
