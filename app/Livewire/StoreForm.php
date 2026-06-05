<?php

namespace App\Livewire;

use App\Models\Store;
use Livewire\Component;

class StoreForm extends Component
{

    public $name;
    public $type;
    public $status;

    public Store $store;

    public function save(){
        $this->store = new Store();
        $this->store->name = $this->name;
        $this->store->type = $this->type;
        $this->store->status = $this->status;
        $this->store->save();
        $this->js('$("#modal-store").modal("hide")');

        $this->dispatch('refresh')->to(StoreView::class);
    }


    public function render()
    {
        return view('livewire.store-form');
    }
}
