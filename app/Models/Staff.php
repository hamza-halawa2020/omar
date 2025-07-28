<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;
    protected $table = 'sm_staffs';

    public function department()
    {
        return $this->belongsTo(SmHumanDepartment::class);
    }

    // public function role()
    // {
    //     return $this->belongsTo(Role::class);
    // }
    // public function department()
    // {
    //     return $this->belongsTo(SmHumanDepartment::class);
    // }
    // public function designation()
    // {
    //     return $this->belongsTo(SmDesignation::class);
    // }
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }
    // public function workExperiences()
    // {
    //     return $this->hasMany(StaffWorkExperience::class);
    // }
    // public function productStaff()
    // {
    //     return $this->hasMany(ProductStaff::class);
    // }
    // public function educations()
    // {
    //     return $this->hasMany(StaffEducation::class);
    // }
    // public function TrackAssignedStaff()
    // {
    //     return $this->hasMany(TrackAssignedStaff::class);
    // }
    // public function slots()
    // {
    //     return $this->hasMany(StaffSlot::class);
    // }
    // public function products()
    // {
    //     return $this->hasMany(ProductStaff::class);
    // }

    // public function ExpertTeacher()
    // {
    //     return $this->hasOne(SmExpertTeacher::class);
    // }
}
