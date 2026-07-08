<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $fillable = [
        'ci',
        'name',
        'email',
        'phone',
    ];
}
