<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class crops extends Model
{
    protected $table = 'crops';

    protected $fillable = [
        'type',
    ];
}
