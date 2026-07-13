<?php

namespace App\Livewire;

use App\Enums\Status;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductView extends Component
{
    public $product_id;

    public $list = [
        'search' => '',
        'sort_field' => 'name',
        'sort_direction' => 'asc'
    ];

    public function updatedProductId()
    {
        $this->dispatch('getBarcode', $this->product_id)->to(ProductForm::class);
        $this->js('$("#modal-scanner").modal("hide");$("#modal-product").modal("show")');
    }

    #[On('refresh')]
    public function render()
    {
        $heads = ['Nombre' =>'name',
            'Modelo' => 'model',
            'Marca' => 'brand_id',
            'Categoria' => 'category_id',
            'Precio (Bs)' => 'price',
            'Acciones' => null
        ];

        $search = $this->list['search'];
        if($search != ''){
            $products = Product::where('name', 'like', '%'.$search.'%')
                ->orWhere('model', 'like', '%'.$search.'%')
                ->orWhereHas('brand', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orWhereHas('category', function ($query) use ($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orWhere('price', 'like', '%'.$search.'%')
                ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->get();
        }else{
            $products = Product::orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->get();
        }
        //$products = Product::all();
        return view('livewire.product-view',compact(['heads','products']));
    }
}
