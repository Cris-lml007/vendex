<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailQuotation extends Model
{
    public $fillable = [
        'quotation_id',
        'product_id',
        'price',
        'quantity'
    ];
}
