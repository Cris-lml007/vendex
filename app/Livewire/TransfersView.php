<?php

namespace App\Livewire;

use App\Models\Transfer;
use Livewire\Component;
use Livewire\WithPagination;

class TransfersView extends Component
{

    use WithPagination;

    public $list = [
        'search' => '',
        'sort_field' => 'id',
        'sort_direction' => 'asc',
        'pages' => 1
    ];

    public function updatedList(){
        if($this->list['pages'] != ''){
            $this->setPage($this->list['pages']);
        }
    }

    public function getTransfer($id){
        $this->dispatch('getTransfer', $id)->to(TransferForm::class);
    }


    public function render()
    {
        $heads = [
            'Id'=> 'id',
            'Fecha' => 'created_at',
            'Movimientos' => null,
            'Acciones' =>null
        ];

        $search = $this->list['search'];

        if($search != ''){
            $data = Transfer::where('id', 'like', '%'.$search.'%')
                ->orWhere('created_at', 'like', '%'.$search.'%')
                ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }else{
            $data = Transfer::orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }

        $this->list['pages_max'] = $data->lastPage();
        return view('livewire.transfers-view', compact(['data', 'heads']));
    }
}
