<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentTab extends Model
{
    protected $table = 'department_tab';

    protected $fillable = [
        'department_id',
        'tab_id'
    ];
}
