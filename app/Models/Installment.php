<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $table = 'installments';

    protected $fillable = [
        'due_date',
        'required_amount',
        'paid_amount',
        'status', // 'pending', 'paid', 'late'
        'installment_contract_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'required_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function contract()
    {
        return $this->belongsTo(InstallmentContract::class, 'installment_contract_id');
    }

    public function payments()
    {
        return $this->hasMany(InstallmentPayment::class, 'installment_id')->orderBy('payment_date');
    }
}
