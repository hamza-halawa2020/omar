<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPurchaseBatch extends Model
{
    protected $fillable = [
        'product_id',
        'transaction_product_id',
        'purchased_quantity',
        'remaining_quantity',
        'unit_cost',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function transactionProduct()
    {
        return $this->belongsTo(TransactionProduct::class, 'transaction_product_id');
    }
}
