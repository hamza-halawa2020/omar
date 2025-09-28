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
}
