<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientDebtLog extends Model
{
    protected $table = 'client_debt_logs';

    protected $fillable = [
        'client_id',
        'debt_before',
        'debt_after',
        'change_amount',
        'source_type',
        'source_id',
        'description',
        'created_by',
    ];

    protected $casts = [
        'debt_before' => 'decimal:2',
        'debt_after' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function source()
    {
        return $this->morphTo();
    }
}
