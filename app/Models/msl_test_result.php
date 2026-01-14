<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class msl_test_result extends Model
{
    protected $table = 'msl_test_results';

    public $timestamps = false; 

    protected $fillable = [
        'id',
        'longitude' ,
        'latitude',
        'farm_area',
        'ph',
        'om',
        'p_bray',
        'p_olsen',
        'k',
        'shc_number',
        'soil_texture',
        'soil_ph_interpretation',
        'year_of_sampling',
        'barangay',
        'municipality',
        'province',
        'status',
    ];
}
