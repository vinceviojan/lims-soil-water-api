<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class landscape extends Model
{
    protected $table = 'landscapes';

    protected $fillable = [
        'type',
    ] ;
}
