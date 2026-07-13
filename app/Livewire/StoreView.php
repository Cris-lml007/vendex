<?php

namespace App\Livewire;

use App\Models\Store;
use Livewire\Attributes\On;
use Livewire\Component;

class StoreView extends Component
{
    #[On('refresh')]
    public function render()
    {
        $stores = Store::all();
        $heads = [
            'Nombre' => 'name',
            'Tipo' => 'type',
            'Estado' => 'state',
            'Acciones' => null
        ];
        return view('livewire.store-view', compact(['heads','stores']));
    }
}
