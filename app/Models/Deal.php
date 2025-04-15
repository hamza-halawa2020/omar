<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Deal extends CrmModel
{
    public function Task(): MorphMany
    {
        return $this->morphMany(Task::class, 'related_to');
    }
}
