<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Enums\Type;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class SaleView extends Component
{
    use WithPagination;

    public $store;
    #public $data;
    public $lock = false;

    public $list = [
        'search' => '',
        'sort_field' => 'Id',
        'sort_direction' => 'asc',
        'pages' => 1
    ];

    public function updatedList(){
        if($this->list['pages'] != ''){
            $this->setPage($this->list['pages']);
        }
    }

    public function updatedStore(){
        #$this->data = Transaction::where('store_id',$this->store)->get();
        $this->render();
    }


    public function getTransaction($id)
    {
        $this->dispatch('getTransaction', $id)->to(SaleForm::class);
    }

    public function render()
    {
        $heads = [
            'Id' => 'id',
            'Cliente' => 'customer_id',
            'Vendedor' => 'user_id',
            'Total' => null,
            'Fecha' => 'created_at',
            'Acciones' => null];
        $stores = Store::where('type', Type::STORE)->get();
        if(Auth::user()->role == Role::SELLER){
            $this->store = Auth::user()->store_id;
            $this->lock = true;
        }

        $search = $this->list['search'];
        if($search != ''){
            $data = Transaction::where('store_id',$this->store)
                ->where(function (Builder $builder) use ($search) {
                    $builder->whereHas('customer', function (Builder $builder) use ($search) {
                        $builder->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('user', function (Builder $builder) use ($search) {
                        $builder->where('name', 'like', '%' . $search . '%');
                    })->orWhere('created_at', 'like', '%' . $search . '%');
                })->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }else{
            $data = Transaction::where('store_id',$this->store)
            ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
            ->paginate();
        }

        $this->list['pages_max'] = $data->lastPage();
        return view('livewire.sale-view', compact(['heads', 'stores', 'data']));
    }
}
