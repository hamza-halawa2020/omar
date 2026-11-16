<?php

namespace App\Models\Umrah;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'umrah_programs';

    protected $guarded = ['id'];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function prices()
    {
        return $this->hasMany(ProgramPrice::class);
    }
}
