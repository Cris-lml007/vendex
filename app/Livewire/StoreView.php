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
        $stores = Store::paginate();
        $heads = ['Nombre','Tipo','Estado','Acciones'];
        return view('livewire.store-view', compact(['heads','stores']));
    }
}
