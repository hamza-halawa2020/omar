<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Contact extends CrmModel
{
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function scopeWithAccount($query)
    {
        return $query->with('account');
    }
}
