<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class maturity extends Model
{
    protected $table = 'maturity';

    protected $fillable = [
        'name',
        'code',
        'date_range',
    ] ;
}
