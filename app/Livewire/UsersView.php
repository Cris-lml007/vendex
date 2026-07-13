<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsersView extends Component
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

    public function getUser($id)
    {
        $this->dispatch('getUser',$id)->to(UsersForm::class);
    }

    public function render()
    {
        $heads = [
            'ID' => 'id',
            'Nombre' => 'name',
            'Rol' => 'role',
            'Tienda' => 'store_id',
            'Estado' => null,
            'Opciones' => null];

        $search = $this->list['search'];
        if($search != ''){
            $data = User::where('name', 'like', '%'.$search.'%')
                ->orWhere('id', 'like', '%'.$search.'%')
                ->orWhereHas('store', function($q) use ($search){
                    $q->where('name', 'like', '%'.$search.'%');
                })
                ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }else{
            $data = User::orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }

        $this->list['pages_max'] = $data->lastPage();
        return view('livewire.users-view', compact('data', 'heads'));
    }
}
