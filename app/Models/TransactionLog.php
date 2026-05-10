<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    protected $table = "transaction_logs";

    protected $fillable = [
        'transaction_id',
        'created_by',
        'action', //create, update, delete
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    protected $appends = [
        'has_payment_way_change',
        'old_payment_way_id',
        'new_payment_way_id',
        'old_payment_way_name',
        'new_payment_way_name',
    ];


    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getHasPaymentWayChangeAttribute(): bool
    {
        if (($this->action ?? null) !== 'update') {
            return false;
        }

        $oldId = data_get($this->data, 'old_data.payment_way_id');
        $newId = data_get($this->data, 'new_data.payment_way_id');

        return $oldId !== null && $newId !== null && (int) $oldId !== (int) $newId;
    }

    public function getOldPaymentWayIdAttribute(): ?int
    {
        $value = data_get($this->data, 'old_data.payment_way_id');

        return $value !== null ? (int) $value : null;
    }

    public function getNewPaymentWayIdAttribute(): ?int
    {
        $value = data_get($this->data, 'new_data.payment_way_id');

        return $value !== null ? (int) $value : null;
    }

    public function getOldPaymentWayNameAttribute(): ?string
    {
        $name = data_get($this->data, 'history.old_payment_way.name')
            ?? data_get($this->data, 'old_data.payment_way.name');

        if ($name) {
            return (string) $name;
        }

        $oldId = $this->old_payment_way_id;
        if (! $oldId) {
            return null;
        }

        return PaymentWay::query()->whereKey($oldId)->value('name');
    }

    public function getNewPaymentWayNameAttribute(): ?string
    {
        $name = data_get($this->data, 'history.new_payment_way.name')
            ?? data_get($this->data, 'new_data.payment_way.name');

        if ($name) {
            return (string) $name;
        }

        $newId = $this->new_payment_way_id;
        if (! $newId) {
            return null;
        }

        return PaymentWay::query()->whereKey($newId)->value('name');
    }
}
