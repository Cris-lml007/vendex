<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    public $fillable = [
        'name',
        'made',
        'color_fg',
        'color_bg'
    ];
}
