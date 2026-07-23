<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class InventoryForm extends Component
{

    public $_id;
    public $quantity = 0;
    public $total = 0;
    public $price;

    public $list;

    public $store_name;
    public $kardex_type;
    public Kardex $kardex;

    public $actions = [
        'search' => '',
        'sort_field' => 'id',
        'sort_direction' => 'asc',
    ];

    #[On('getBarcode')]
    public function getBarcode($id){
        $this->_id = $id;
    }

    public function updatedQuantity(): void{
        $this->total = 0;
        $this->list = [];
        //$this->setStock(0,0);
    }

    public function setStock($id, $value): void{
        $this->total = 0;//$this->quantity;
        $this->list[$id] = (int) $value;
        foreach($this->list as $item){
            $this->total += $item;
        }
    }

    #[On('getKardex')]
    public function getKardex($id){
        $this->kardex = Kardex::find($id);
        $this->_id = $this->kardex->product_id;
        $this->quantity = $this->kardex->quantity;
        $this->price = $this->kardex->price;
        $store = Store::find($this->kardex->store_id);
        $this->store_name = $store->name;
        $this->kardex_type = $this->kardex->type;
    }

    public function restart()
    {
        $this->_id = null;
        $this->quantity = 0;
        $this->price = 0;
        $this->list = [];
        $this->store_name = null;
        $this->kardex_type = null;
        $this->kardex = new Kardex();
    }

    public function save(): void
    {
        $this->validate([
            '_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required',
            'total' => 'required|integer|min:'.$this->quantity.'|max:'.$this->quantity
        ], attributes: [
            '_id' => 'producto'
        ]);
        try{
            DB::transaction(function () {
                foreach($this->list as $item => $value){
                    Kardex::create([
                        'store_id' => $item,
                        'product_id' => $this->_id,
                        'quantity' => $value,
                        'price' => $this->price,
                        'type' => Type::IN,
                        'user_id' => Auth::user()->id
                    ]);

                    Stock::updateOrCreate([
                        'product_id' => $this->_id,
                        'store_id' => $item,
                    ],[
                        'quantity' => $value + (Stock::where('product_id',$this->_id)->where('store_id',$item)?->first()?->quantity ?? 0),
                    ]);
                }
            });
        }catch (\Throwable $e) {
            dd($e->getmessage());
        }

        $this->js('$("#modal-inventory").modal("hide")');
        $this->dispatch('refresh')->to(InventoryView::class);
    }


    public function render()
    {
        $heads = [
            'Nombre' => 'name',
            'Tipo' => 'type',
            'Cantidad' => 'quantity',
        ];
        $stores = Store::all();
        $products = Product::all();
        return view('livewire.inventory-form',compact('stores','products','heads'));
    }
}
