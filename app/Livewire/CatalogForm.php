<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class CatalogForm extends Component
{

    public $name;
    public $price;
    public $category;
    public $description;
    public $brand;
    public $model;

    #[On('getProduct')]
    public function getProduct($id)
    {
        $product = Product::find($id);
        $this->name = $product->name;
        $this->price = $product->price;
        $this->category = $product->category->name;
        $this->description = $product->description;
        $this->brand = $product->brand->name;
        $this->model = $product->model;
    }

    public function render()
    {
        return view('livewire.catalog-form');
    }
}
