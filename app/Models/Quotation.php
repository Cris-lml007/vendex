<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Quotation extends Model
{
    public $fillable = [
        'valid_to',
        'valid_from',
        'user_id'
    ];

    protected static function booted(): void
    {
        static::created(function (Quotation $quotation){
            if(Auth::check()){
                $quotation->user_id = Auth::user()->id;
            }
        });
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id','id');
    }
}
