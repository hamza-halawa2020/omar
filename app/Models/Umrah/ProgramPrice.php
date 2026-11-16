<?php

namespace App\Models\Umrah;

use Illuminate\Database\Eloquent\Model;

class ProgramPrice extends Model
{
    protected $table = 'umrah_program_prices';

    protected $guarded = ['id'];

    protected $casts = ['amount' => 'decimal:2', 'is_active' => 'boolean'];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
