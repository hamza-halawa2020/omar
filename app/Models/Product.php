<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'code',
        'image',
        'description',
        'purchase_price',
        'sale_price',
        'stock',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function installmentContracts()
    {
        return $this->hasMany(InstallmentContract::class, 'product_id');
    }

     public function transactions()
    {
        return $this->hasMany(Transaction::class, 'product_id')->latest();
    }
}
