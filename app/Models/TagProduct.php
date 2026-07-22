<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagProduct extends Model
{
    public $fillable = [
        'product_id',
        'name',
        'value'
    ];
}
