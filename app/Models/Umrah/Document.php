<?php

namespace App\Models\Umrah;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'umrah_documents';

    protected $guarded = ['id'];

    public function traveler()
    {
        return $this->belongsTo(Traveler::class);
    }
}
