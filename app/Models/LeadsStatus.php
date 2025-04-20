<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadsStatus extends CrmModel
{
    public function parentStatus(): BelongsTo
    {
        return $this->belongsTo(LeadsStatus::class, 'parent_id');
    }

    public function scopeWhereParentStatusId($query, int $parentId)
    {
        return $query->where('parent_id', $parentId);
    }
}
