<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Iphone extends Model
{
    protected $table = 'iphones';

    protected $fillable = [
        'device_type',
        'device_details',
        'purchase_price_sar',
        'currency',
        'purchase_price_egp',
        'extra_expenses',
        'total_purchase_with_expenses',
        'sale_price_egp',
        'net_profit_after_sale',
        'status',
        'created_by',
    ];

    protected $casts = [
        'purchase_price_sar' => 'decimal:2',
        'purchase_price_egp' => 'decimal:2',
        'extra_expenses' => 'decimal:2',
        'total_purchase_with_expenses' => 'decimal:2',
        'sale_price_egp' => 'decimal:2',
        'net_profit_after_sale' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function logs()
    {
        return $this->hasMany(IphoneLog::class, 'iphone_id')->latest();
    }
}
