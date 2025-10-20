<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'payment_way_id',
        'created_by',
        'type',     // send, receive
        'amount',
        'commission',
        'notes',
        'attachment',
        'client_id',
        'product_id',
        'quantity',
        'balance_before_transaction',
        'balance_after_transaction',
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

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function installmentPayment()
    {
        return $this->hasMany(InstallmentPayment::class, 'transaction_id');
    }
    public function associationPayment()
    {
        return $this->hasMany(AssociationPayment::class, 'transaction_id');
    }
}
