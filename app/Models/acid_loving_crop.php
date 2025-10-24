<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class acid_loving_crop extends Model
{
     protected $table = 'acid_loving_crops';

    protected $fillable = [
        'crops',
        'category_code',
        'min_ph',
        'max_ph',
        'category',
        
    ];
}
