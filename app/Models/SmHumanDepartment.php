<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmHumanDepartment extends Model
{
    use HasFactory;

    protected $table = 'sm_human_departments';

    protected $fillable = [
        'name',
        'mail_alias',
        'parent_id',
        'department_head_id',
        'slug',
    ];
    
    public static function boot()
    {
        parent::boot();
        // static::addGlobalScope(new ActiveStatusSchoolScope);
    }
    public function staffs()
    {
        return $this->hasMany(Staff::class, 'department_id');
    }
}
