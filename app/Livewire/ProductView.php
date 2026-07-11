<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductView extends Component
{
    public $product_id;

    public function updatedProductId()
    {
        $this->dispatch('getBarcode', $this->product_id)->to(ProductForm::class);
        $this->js('$("#modal-scanner").modal("hide");$("#modal-product").modal("show")');
    }

    #[On('refresh')]
    public function render()
    {
        $heads = ['Nombre', 'Modelo', 'Marca', 'Categoria','Precio (Bs)', 'Acciones'];
        $products = Product::paginate();
        return view('livewire.product-view',compact(['heads','products']));
    }
}
