<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends CrmModel
{


    protected $table = 'crm_calls';


    public function callStatus()
    {
        return $this->belongsTo(CallStatus::class, 'call_status_id');
    }



    public function relatedTo()
    {
        return $this->morphTo();
    }



}
