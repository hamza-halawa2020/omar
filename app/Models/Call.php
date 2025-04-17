<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends CrmModel
{
    public function relatedTo()
    {
        return $this->morphTo();
    }
}
