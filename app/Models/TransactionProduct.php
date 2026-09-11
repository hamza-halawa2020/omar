<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionProduct extends Model
{
    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'unit_price',
        'total',
        'cost_total',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function purchaseBatch()
    {
        return $this->hasOne(ProductPurchaseBatch::class, 'transaction_product_id');
    }

    public function batchAllocations()
    {
        return $this->hasMany(TransactionProductBatchAllocation::class, 'transaction_product_id');
    }
}
