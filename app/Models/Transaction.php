<?php

namespace App\Models;

use App\Enums\Type;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $fillable = [
        'customer_id',
        'user_id',
        'store_id',
        'payment_method',
        'observation',
    ];

    protected function casts(): array
    {
        return [
            'payment_method' => Type::class
        ];
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function store(){
        return $this->belongsTo(Store::class);
    }

    public function details(){
        return $this->hasMany(DetailTransaction::class, 'transaction_id', 'id');
    }

    public function total(): Attribute{
        return Attribute::make(
            get: function(){
                return $this->details()->selectRaw('SUM(price*quantity) as total')->first()->total;
            }
        );
    }

    public function totalBs():Attribute{
        return Attribute::make(
            get: function(){
                return $this->details()
                    ->join('exchange_rates','exchange_rates.id','=','detail_transactions.exchange_rate_id')
                    ->selectRaw('SUM(price*quantity*exchange_rates.usd_to_bs) as total')
                    ->first()
                    ->total;
            }
        );
    }
}
