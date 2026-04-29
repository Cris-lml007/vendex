<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductView extends Component
{
    #[On('refresh')]
    public function render()
    {
        $heads = ['Nombre', 'Modelo', 'Marca', 'Categoria','Precio (Bs)', 'Acciones'];
        $products = Product::paginate();
        return view('livewire.product-view',compact(['heads','products']));
    }
}
