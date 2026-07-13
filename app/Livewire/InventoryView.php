<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Kardex;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryView extends Component
{

    use WithPagination;

    public $last;

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

    public function getKardex($id){
        $this->dispatch('getKardex',$id)->to(InventoryForm::class);
    }

    public function mount(){
        $this->last = Kardex::latest('id')->first()->id ?? null;
    }


    public function remove($password, $id){
        if($this->last != $id){
            $this->last = Kardex::latest('id')->first()->id ?? null;
            return false;
        }
        if(Hash::check($password, Auth::user()->password)){
            $this->kardex = Kardex::find($id);
            $stock = Stock::where('product_id', $this->kardex->product_id)
                ->where('store_id', $this->kardex->store_id)
                ->first();
            if($this->kardex->type == Type::IN){
                $stock->quantity -= $this->kardex->quantity;
            }else{
                $stock->quantity += $this->kardex->quantity;
            }
            $stock->save();
            $this->kardex->delete();
            $this->last = Kardex::latest('id')->first()->id ?? null;
            return true;
        }

        $this->last = Kardex::latest('id')->first()->id ?? null;
        return false;
    }

    #[On('refresh')]
    public function render()
    {
        $heads = [
            'Id' => 'id',
            'Nombre' => 'name',
            'Precio de Adquisición' => 'price',
            'Precio de Venta' => 'price',
            'Cantidad' => 'quantity',
            'Tipo' => 'type',
            'Locación' => 'name',
            'Fecha' => 'created_at',
            'Acciones' => null
        ];

        $search = $this->list['search'];
        if($search != ''){
            $data = Kardex::Where('id','like', '%'.$search.'%')
                ->orWhere('quantity', 'like', '%'.$search.'%')
                ->orWhereRaw('(quantity*price) like ?', '%'.$search.'%')
                ->orWhere('type', 'like', '%'.$search.'%')
                ->orWhereHas('product', function($query) use ($search){
                    $query->where('name', 'like', '%'.$search.'%');
                })
                ->orWhere('created_at', 'like', '%'.$search.'%')
                ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }else{
            $data = kardex::orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }
        $this->list['pages_max'] = $data->lastPage();
        return view('livewire.inventory-view', compact(['heads','data']));
    }
}
