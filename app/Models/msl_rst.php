<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class msl_rst extends Model
{
    protected $table = 'msl_rst';

    protected $fillable = [
        'farm_area',
        'longitude',
        'latitude',
        'soil_texture ',
        'ph',
        'soil_ph_interpretation',
        'n',
        'p',
        'k',
        'shc_number',
        'shc_number' => 'required|string|max:100',
    ];

    public $timestamps = false;
}
