<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Attributes\On;
use Livewire\Component;

class CustomersForm extends Component
{
    public $name;
    public $email;
    public $phone;
    public $ci;

    public Customer $customer;

    public function mount(Customer $customer = null)
    {
        if($customer?->id != null){
            $this->customer = $customer;
            $this->name = $customer->name;
            $this->phone = $customer->phone;
            $this->ci = $customer->ci;
            $this->email = $customer->email;
        }else{
            $this->customer = new Customer();
        }
    }

    #[On('getCustomer')]
    public function getCustomer($id)
    {
        $this->customer = Customer::find($id);

        if($this->customer?->id != null) {
            $this->name = $this->customer->name;
            $this->phone = $this->customer->phone;
            $this->ci = $this->customer->ci;
            $this->email = $this->customer->email;
        }else{
            $this->name = null;
            $this->phone = null;
            $this->ci = null;
            $this->email = null;
            $this->customer = new Customer();
        }
    }

    public function save(){
        $this->validate([
            'name' => 'required',
            'phone' => 'required',
            'ci' => 'required|unique:customers,ci,'. $this->customer?->id ?? null,
        ]);

        if($this->customer?->id == null){
            $this->customer = new Customer();
        }

        $this->customer->name = $this->name;
        $this->customer->phone = $this->phone;
        $this->customer->email = $this->email;
        $this->customer->ci = $this->ci;
        $this->customer->save();

        $this->redirect(route('admin.customers'));
    }

    public function render()
    {
        return view('livewire.customers-form');
    }
}
