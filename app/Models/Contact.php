<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Contact extends CrmModel
{
    protected $appends = ['name'];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'related_to');
    }

    public function scopeWithAccount($query)
    {
        return $query->with('account');
    }

    public function getNameAttribute(): string
    {
        return "$this->first_name $this->last_name";
    }
}
