<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    public $fillable = [
        'product_id',
        'store_id',
        'quantity',
    ];
}
