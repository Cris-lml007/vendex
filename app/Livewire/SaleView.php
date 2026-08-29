<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Enums\Type;
use App\Models\ExchangeRate;
use App\Models\Store;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SaleView extends Component
{
    use WithPagination;

    public $store;
    #public $data;
    public $lock = false;
    public $today = true;
    public $total = 0;

    public $list = [
        'search' => '',
        'sort_field' => 'Id',
        'sort_direction' => 'asc',
        'pages' => null
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


        $search = $this->list['search'];
        if(Auth::user()->role == Role::SELLER){
            $this->store = Auth::user()->store_id;
            $this->lock = true;

            if($search != ''){
                $data = Auth::user()->sales()->where('store_id',$this->store)
                    ->where(function (Builder $builder) use ($search) {
                        $builder->whereHas('customer', function (Builder $builder) use ($search) {
                            $builder->where('name', 'like', '%' . $search . '%');
                        })->orWhere(function ($b) use ($search){
                                $b->where('created_at', 'like', '%' . $search . '%')
                                    ->when($this->today,function ($q){
                                        $q->whereDate('created_at',today());
                                    });
                            });
                    })->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                    ->get();
            }else{
                $data = Auth::user()->sales()->where('store_id',$this->store)
                    ->when($this->today,function($q){
                        $q->whereDate('created_at',today());
                    })->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                    ->get();
            }
        }else{
            if($search != ''){
                $data = Transaction::where('store_id',$this->store)
                    ->where(function (Builder $builder) use ($search) {
                        $builder->whereHas('customer', function (Builder $builder) use ($search) {
                            $builder->where('name', 'like', '%' . $search . '%');
                        })->orWhereHas('user', function (Builder $builder) use ($search) {
                            $builder->where('name', 'like', '%' . $search . '%');
                        })->orWhere(function ($b) use ($search){
                                $b->where('created_at', 'like', '%' . $search . '%')
                                    ->when($this->today,function ($q){
                                        $q->whereDate('created_at',today());
                                    });
                            });
                    })->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                    ->get();
            }else{
                $data = Transaction::where('store_id',$this->store)
                    ->when($this->today,function ($q){
                        $q->whereDate('created_at',today());
                    })->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                    ->get();
                $this->total = $data->sum('totalBs');
                // dd($this->total);
            }
        }


        $this->list['pages_max'] = null;//$data->lastPage();
        return view('livewire.sale-view', compact(['heads', 'stores', 'data']));
    }
}
