<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Account extends CrmModel
{
    protected $appends = ['name'];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeWithAssignedUser($query)
    {
        return $query->with(['assignedUser']);
    }

    public function getNameAttribute(): string
    {
        return "$this->first_name $this->last_name";
    }
}
