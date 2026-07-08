<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;

class CustomersView extends Component
{

    public function getCustomer($id){
        $this->dispatch('getCustomer',$id)->to(CustomersForm::class);
    }


    public function render()
    {
        $data = Customer::all();
        return view('livewire.customers-view', compact(['data']));
    }
}
