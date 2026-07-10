<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransfer extends Model
{
    public $fillable = [
        'transfer_id',
        'product_id',
        'quantity',
        'store_id',
        'kardex_id',
    ];

    public function transfer(){
        return $this->belongsTo(Transfer::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function kardex(){
        return $this->belongsTo(Kardex::class);
    }

    public function store(){
        return $this->belongsTo(Store::class);
    }
}
