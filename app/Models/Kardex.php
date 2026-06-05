<?php

namespace App\Models;

use App\Enums\Type;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    public $fillable = [
        'product_id',
        'store_id',
        'quantity',
        'price',
        'type'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function store(){
        return $this->belongsTo(Store::class);
    }

    protected function casts(): array
    {
        return [
            'type' => Type::class
        ];
    }
}
