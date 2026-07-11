<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public $fillable = [
        'customer_id',
        'user_id',
        'store_id'
    ];

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
}
