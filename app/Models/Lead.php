<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends CrmModel
{
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function Task()
    {
        return $this->morphMany(Task::class, 'related_to');
    }

    public function scopeWithAssignedUsers($query)
    {
        return $query->with('assignedUser');
    }

    public function scopeWhereAvailable($query)
    {
        return $query->where('is_converted', false);
    }

    public function scopeWhereConverted($query) {
        return $query->where('is_converted', true);
    }
}
