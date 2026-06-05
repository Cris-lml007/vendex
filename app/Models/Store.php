<?php

namespace App\Models;

use App\Enums\Status;
use App\Enums\Type;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected function casts(): array
    {
        return [
            'type' => Type::class,
            'status' => Status::class
        ];
    }
}
