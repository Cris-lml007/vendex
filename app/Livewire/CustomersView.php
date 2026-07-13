<?php

namespace App\Livewire;

use App\Models\Customer;
use Livewire\Component;
use Livewire\WithPagination;

class CustomersView extends Component
{

    use withPagination;

    public $list = [
        'search' => '',
        'sort_direction' => 'asc',
        'sort_field' => 'ci',
        'pages' => 1
    ];

    public function updatedList(){
        if($this->list['pages'] != ''){
            $this->setPage($this->list['pages']);
        }
    }

    public function getCustomer($id){
        $this->dispatch('getCustomer',$id)->to(CustomersForm::class);
    }


    public function render()
    {
        $heads = [
            'CI' => 'ci',
            'Nombre Completo' => 'name',
            'Celular' => 'phone',
            'Correo Electronico' => 'email',
            'Acciones' => null
        ];
        $search = $this->list['search'];
        if($search != ''){
            $data = Customer::where('ci','like','%'.$search.'%')
                ->orWhere('name','like','%'.$search.'%')
                ->orWhere('phone','like','%'.$search.'%')
                ->orWhere('email','like','%'.$search.'%')
                ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }else{
            $data = Customer::orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }
        $this->list['pages_max'] = $data->lastPage();
        return view('livewire.customers-view', compact(['data','heads']));
    }
}
