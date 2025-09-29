<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentContract extends Model
{
    protected $table = 'installment_contracts';

    protected $fillable = [
        'product_price',
        'down_payment',
        'remaining_amount',
        'installment_count',
        'interest_rate',
        'interest_amount',
        'total_amount',
        'installment_amount',
        'start_date',
        'client_id',
        'product_id',
        'created_by',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function installments()
    {
        return $this->hasMany(Installment::class, 'installment_contract_id')->orderBy('due_date');
    }

    public function getRemainingInstallmentsAttribute()
    {
        return $this->installments()->where('status', '!=', 'paid')->count();
    }

    public function getRemainingAmountAttribute()
    {
        return $this->installments->sum(function ($installment) {
            return $installment->required_amount - $installment->paid_amount;
        });
    }
}
