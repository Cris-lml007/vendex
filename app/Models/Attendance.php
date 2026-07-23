<?php

namespace App\Models;

use App\Enums\Type;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    public $fillable = [
        'user_id',
        'type',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'type' => Type::class,
        ];
    }
}
