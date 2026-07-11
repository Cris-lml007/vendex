<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Kardex;
use App\Models\Stock;
use App\Models\Store;
use Livewire\Component;

class StoreForm extends Component
{

    public $name;
    public $type;
    public $status;

    public Store $store;
    public $stock;
    public $sales;
    public $edit = false;

    public $address;
    public $phone;
    public $email;

    public function mount(Store $store = null){
        if($store->id != null){
            $this->edit = true;
            $this->store = $store;
            $this->name = $store->name;
            $this->type = $store->type;
            $this->status = $store->status;
            $this->address = $store->address;
            $this->phone = $store->phone;
            $this->email = $store->email;

            $this->stock = $this->store->products;
            $this->sales = Kardex::where('store_id', $this->store->id)->where('type',Type::OUT)->get();
        }else{
            $this->store = new Store();
        }
    }

    public function save(){
        $this->store = new Store();
        $this->store->name = $this->name;
        $this->store->type = $this->type;
        $this->store->status = $this->status;
        $this->store->address = $this->address;
        $this->store->phone = $this->phone;
        $this->store->email = $this->email;
        $this->store->save();
        $this->js('$("#modal-store").modal("hide")');

        $this->dispatch('refresh')->to(StoreView::class);
    }


    public function render()
    {
        return view('livewire.store-form');
    }
}
