<?php

namespace App\Livewire;

use App\Models\Kardex;
use Livewire\Attributes\On;
use Livewire\Component;

class InventoryView extends Component
{
    #[On('refresh')]
    public function render()
    {
        $heads = ['Id', 'Nombre', 'Precio de Adquisición', 'Precio de Venta', 'Cantidad', 'Tipo', 'Locación','Fecha' ,'Acciones'];
        $data = Kardex::all();
        return view('livewire.inventory-view', compact(['heads','data']));
    }
}
