<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SmHumanDepartment;

class Tab extends Model
{

    public $timestamps = true;
    protected $fillable = [
        'label',
        'url',
        'icon',
        'parent_id',
        'permission_required',
        'order'
    ];
    public function children()
    {
        return $this->hasMany(Tab::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Tab::class, 'parent_id');
    }

    public function departments()
    {
        return $this->belongsToMany(SmHumanDepartment::class, 'department_tab', 'tab_id', 'department_id');
    }
}
