<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPayment extends Model
{
    protected $table = 'installment_payments';

    protected $fillable = [
        'installment_id',
        'transaction_id',
        'amount',
        'payment_date',
        'paid_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function installment()
    {
        return $this->belongsTo(Installment::class, 'installment_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function paid_by()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
