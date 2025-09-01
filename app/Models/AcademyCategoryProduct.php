<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademyCategoryProduct extends Model
{
    use HasFactory;
    protected $table = 'academy_category_products';
    protected $fillable = [
        'name_en',
        'name_ar',
        'slug'
    ];
}
