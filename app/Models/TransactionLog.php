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


    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
