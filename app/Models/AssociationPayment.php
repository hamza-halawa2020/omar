<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssociationPayment extends Model
{
    protected $table = 'association_payments';

    protected $fillable = [
        'association_id',
        'member_id',
        'amount',
        'payment_date',
        'status', // e.g., 'paid', 'pending', 'late'
        'created_by',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class, 'association_id');
    }

    public function member()
    {
        return $this->belongsTo(AssociationMember::class, 'member_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
