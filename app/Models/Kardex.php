<?php

namespace App\Models;

use App\Enums\Type;
use Illuminate\Database\Eloquent\Model;
use League\CommonMark\Reference\Reference;

class Kardex extends Model
{
    public $fillable = [
        'product_id',
        'store_id',
        'quantity',
        'price',
        'type',
        'user_id'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function store(){
        return $this->belongsTo(Store::class);
    }

    public function referenceable(){
        return $this->morphTo();
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'type' => Type::class
        ];
    }
}
