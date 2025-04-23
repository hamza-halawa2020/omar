<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkFlow extends Model
{
    use HasFactory;

    protected $table = 'crm_work_flows';

    protected $fillabel = [
        'name',
        'type'
    ];

    public function callStatus()
    {
        return $this->hasMany(CallStatus::class, 'workflow_id');
    }
}
