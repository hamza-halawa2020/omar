<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends CrmModel
{
    public function relatedTo(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopeWithRelatedTo($query)
    {
        return $query->with('relatedTo');
    }

    public function scopeWithAssignedUser($query)
    {
        return $query->with('assignedUser');
    }
}
