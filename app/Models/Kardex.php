<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\Type;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'user_id',
        'exchange_rate_id',
    ];


    public function price(): Attribute{
        return Attribute::make(
            get: fn($value) => Settings::first()->currency_main == Currency::BS ? $value * ExchangeRate::orderBy('id','desc')->first()->usd_to_bs : $value,
            set: fn($value) => Settings::first()->currency_main == Currency::BS ? (float)$value / (float)ExchangeRate::orderBy('id','desc')->first()->usd_to_bs : $value
        );
    }

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

    public function exchange_rate()
    {
        return $this->belongsTo(ExchangeRate::class,'exchange_rate_id','id');
    }

    protected function casts(): array
    {
        return [
            'type' => Type::class
        ];
    }
}
