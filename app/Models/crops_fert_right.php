<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class crops_fert_right extends Model
{
    protected $table = 'crops_fert_right';

    protected $fillable = [
        'crop_type',
        'shc_number',
    ];

    public $timestamps = false;
}
