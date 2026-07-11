<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class DetailTransaction extends Model
{
    public $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function kardex(){
        return $this->morphOne(Kardex::class, 'referenceable');
    }

    public function transaction(){
        return $this->belongsTo(Transaction::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function subtotal(): Attribute{
        return Attribute::make(
            get: function(){
                return $this->price*$this->quantity;
            }
        );
    }
}
