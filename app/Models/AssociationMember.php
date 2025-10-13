<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssociationMember extends Model
{
    protected $table = 'association_members';

    protected $fillable = [
        'association_id',
        'client_id',
        'payout_order',
        'has_received',
        'receive_date',
        'amount'
    ];

    protected $casts = [
        'has_received' => 'boolean',
    ];

    public function association()
    {
        return $this->belongsTo(Association::class, 'association_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function payments()
    {
        return $this->hasMany(AssociationPayment::class, 'member_id');
    }
}
