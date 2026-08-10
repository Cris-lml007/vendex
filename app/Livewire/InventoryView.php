<?php

namespace App\Livewire;

use App\Enums\Role;
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
        'sort_field' => 'created_at',
        'sort_direction' => 'desc',
        'pages' => 1
    ];

    public $product_id;
    public $current = 2;

    public function updatedList(){
        if($this->list['pages'] != ''){
            $this->setPage($this->list['pages']);
        }
    }

    public function updatedProductId()
    {
        $this->dispatch('getBarcode', $this->product_id)->to(InventoryForm::class);
        $this->js('$("#modal-scanner").modal("hide");$("#modal-inventory").modal("show")');
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
        if(Hash::check($password, Auth::user()->password) && Auth::user()->role == Role::ADMIN){
            $this->kardex = Kardex::find($id);
            $stock = Stock::where('product_id', $this->kardex->product_id)
                ->where('store_id', $this->kardex->store_id)
                ->first();
            if(!$this->kardex->product->is_serialize){
                if($this->kardex->type == Type::IN){
                    $stock->quantity -= $this->kardex->quantity;
                }else{
                    $stock->quantity += $this->kardex->quantity;
                }
                $stock->save();
            }else{
                $this->kardex->product->delete();
            }
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
            'Por' => null,
            'Fecha' => 'created_at',
            'Acciones' => null
        ];

        $search = $this->list['search'];
        if($search != ''){
            $terms = preg_split('/\s+/', trim($search));

            $data = Kardex::where(function ($query) use ($terms){

                foreach ($terms as $term) {
                    $query->where(function ($q) use ($term) {
                            $q->orWhere(function($q1) use ($term){
                                foreach (Type::cases() as $i){
                                    if(stripos(__('messages.'.$i->name),$term) !== false){
                                        $q1->where('type', $i->value);
                                    }
                                }
                            })->orWhere('price', 'like', "%{$term}%")
                            ->orWhere('quantity', 'like', "%{$term}%")
                            ->orWhere('id', 'like', "%{$term}%")
                            ->orWhereRaw('(quantity*price) like ?', "%{$term}%")
                            ->orWhereHas('product', function($query) use ($term){
                                $query->where('name', 'like', '%'.$term.'%')
                                    ->orWhere('id', 'like', '%'.$term.'%')
                                    ->orWhere('color', 'like', '%'.$term.'%');
                            })->orWhereHas('user', function($query) use ($term){
                                $query->where('name', 'like', '%'.$term.'%');
                            })->orWhere('created_at', 'like', '%'.$term.'%')
                                ->orWhereHas('store', function($query) use ($term){
                                    $query->where('name', 'like', '%'.$term.'%');
                                });
                    });
                }



            })->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
            //Where('id','like', '%'.$search.'%')
            //    ->orWhere('quantity', 'like', '%'.$search.'%')
            //    ->orWhereRaw('(quantity*price) like ?', '%'.$search.'%')
            //    ->orWhere('type', 'like', '%'.$search.'%')
            //    ->orWhereHas('product', function($query) use ($search){
            //        $query->where('name', 'like', '%'.$search.'%');
            //    })->orWhereHas('user', function($query) use ($search){
            //        $query->where('name', 'like', '%'.$search.'%');
            //    })->orWhere('created_at', 'like', '%'.$search.'%')
        }else{
            $data = kardex::orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }
        $this->list['pages_max'] = $data->lastPage();
        return view('livewire.inventory-view', compact(['heads','data']));
    }
}
