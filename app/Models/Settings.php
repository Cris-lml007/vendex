<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    public $fillable = [
        'wholesale_price',
        'serialized_products',
        'heredaded_products',
        'transfers_all',
        'change_password',
        'tutorial',
        'theme',
        'product_tags'
    ];

    protected function cast(): array {
        return [
            'product_tags' => 'array'
        ];
    }
}
