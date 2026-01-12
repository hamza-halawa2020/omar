<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';

    protected $fillable = [
        'name',
        'phone_number',
        'debt',
        'created_by',
        'type' //client, merchant
    ];

    protected $casts = [
        'debt' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'client_id')->latest();
    }

    public function installmentContracts()
    {
        return $this->hasMany(InstallmentContract::class, 'client_id');
    }

    public function getTotalRemainingAmountAttribute()
    {
        return $this->installmentContracts->sum(function ($contract) {
            return $contract->installments->sum(function ($installment) {
                return $installment->required_amount - $installment->paid_amount;
            });
        });
    }

    public function getTotalRemainingInstallmentsAttribute()
    {
        return $this->installmentContracts->sum(function ($contract) {
            return $contract->installments->where('status', '!=', 'paid')->count();
        });
    }


    public function associations()
    {
        return $this->hasMany(AssociationMember::class, 'client_id');
    }

    public function debtLogs()
    {
        return $this->hasMany(ClientDebtLog::class, 'client_id')->latest();
    }

}
