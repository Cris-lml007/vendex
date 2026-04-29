<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class ProductForm extends Component
{

    public $name;
    public $price;
    public $category;
    public $description;
    public $brand;
    public $model;
    public $barcode;

    public Product $product;

    public function mount(Product $product = null){
        $this->product = $product;
        if($this->product != null){
            $this->barcode = $this->product->id;
            $this->name = $this->product->name;
            $this->price = $this->product->price;
            $this->category = $this->product->category_id;
            $this->description = $this->product->description;
            $this->barcode = $this->product->barcode;
            $this->brand = $this->product->brand;
            $this->model = $this->product->model;
        }else{
            $this->product = new Product();
        }
    }

    public function save(){
        if($this->product->id == null){
            $this->product = new Product();
        }
        $this->product->name = $this->name;
        $this->product->price = $this->price;
        $this->product->description = $this->description;
        $this->product->brand = $this->brand;
        $this->product->model = $this->model;
        $this->product->category_id = $this->category;
        if($this->barcode != ''){
            $this->product->id = $this->barcode;
        }
        $this->product->save();
        $this->reset();

        $this->js('$("#modal-product").modal("hide")');

        $this->dispatch('refresh')->to(ProductView::class);
    }

    public function render()
    {
        $categories = Category::all();
        return view('livewire.product-form')->with('categories',$categories);
    }
}
