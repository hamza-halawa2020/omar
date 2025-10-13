<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Association extends Model
{
    protected $table = 'associations';

    protected $fillable = [
        'name',
        'per_day',//كل كام يوم يعني كل شهر ولا اسبوع
        'total_members', 
        'monthly_amount', // المبلغ الشهري المطلوب
        'start_date',
        'end_date',
        'status', //  'active', 'completed', 'paused'
        'created_by',
    ];

    protected $casts = [
        'monthly_amount' => 'decimal:2',
        'per_day' => 'integer',
    ];
    

    public function members()
    {
        return $this->hasMany(AssociationMember::class, 'association_id')->orderBy('payout_order');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'association_id');
    }

    public function payments()
    {
        return $this->hasMany(AssociationPayment::class, 'association_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
