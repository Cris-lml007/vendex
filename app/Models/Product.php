<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuids;

    public $fillable = [
        'store_id'
    ];

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id','id');
    }

    public function stocks(){
        return $this->hasMany(Stock::class,'product_id','id');
    }

    protected function casts(): array
    {
        return [
            'status' => Status::class,
        ];
    }
    public function tags(){
        return $this->hasMany(TagProduct::class,'product_id','id');
    }

    public function parent(){
        return $this->belongsTo(Product::class,'parent_id','id');
    }

    public function children(){
        return $this->hasMany(Product::class,'parent_id','id');
    }

    public function kardex(){
        if($this->is_serialize)
            return $this->hasOne(Kardex::class,'product_id','id');
        else
            return $this->hasMany(Kardex::class,'product_id','id');
    }

    public function details()
    {
        return $this->hasMany(DetailTransaction::class,'product_id','id');
    }

    public function store(){
        return $this->belongsTo(Store::class,'store_id','id');
    }
}
