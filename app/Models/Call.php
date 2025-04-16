<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends CrmModel
{
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function scopeWithContact($query)
    {
        return $query->with('contact');
    }
}
