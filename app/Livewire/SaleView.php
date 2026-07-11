<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class SaleView extends Component
{

    public $store;
    public $data;

    public function updatedStore(){
        $this->data = Transaction::where('store_id',$this->store)->get();
    }


    public function getTransaction($id)
    {
        $this->dispatch('getTransaction', $id)->to(SaleForm::class);
    }

    public function render()
    {
        $heads = ['Id','Cliente','Vendedor','Total', 'Fecha', 'Acciones'];
        $stores = Store::where('type', Type::STORE)->get();
        return view('livewire.sale-view', compact(['heads', 'stores']));
    }
}
