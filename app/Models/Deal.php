<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Deal extends CrmModel
{
    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'related_to');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function scopeWithAccount($query)
    {
        return $query->with('account');
    }

    public function scopeWithContact($query) {
        return $query->with('contact');
    }

    public function withTasks($query)
    {
        return $query->with('tasks');
    }
}
