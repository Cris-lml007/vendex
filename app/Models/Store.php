<?php

namespace App\Models;

use App\Enums\Status;
use App\Enums\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    protected function casts(): array
    {
        return [
            'type' => Type::class,
            'status' => Status::class
        ];
    }

    public function products(){
        return $this->belongstomany(Product::class,'stocks')->withPivot('quantity');
    }
}
