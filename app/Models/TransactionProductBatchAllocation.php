<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionProductBatchAllocation extends Model
{
    protected $fillable = [
        'transaction_product_id',
        'product_purchase_batch_id',
        'quantity',
        'unit_cost',
        'total',
    ];

    public function transactionProduct()
    {
        return $this->belongsTo(TransactionProduct::class, 'transaction_product_id');
    }

    public function purchaseBatch()
    {
        return $this->belongsTo(ProductPurchaseBatch::class, 'product_purchase_batch_id');
    }
}
