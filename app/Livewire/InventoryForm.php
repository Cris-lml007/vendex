<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\ExchangeRate;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use function PHPUnit\Framework\isNull;

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

    public $bs;
    public $usd;

    public function updatedBs(){
        //verificar que solo sea numberos y puntos
        if(!is_numeric($this->bs)){
            $this->bs = 0;
            $this->usd = 0;
            return;
        }

        if($this->bs != ''){
            if($this?->kardex?->id != null){
                $this->usd = $this->bs / $this->kardex->exchange_rate->usd_to_bs;
            }else{
                $this->usd = $this->bs / ExchangeRate::orderBy('id','desc')->first()->usd_to_bs;
            }
            $this->price = $this->usd;
            $this->usd = round($this->usd,2);
        }else{
            $this->usd = 0;
            $this->price = 0;
        }
    }

    public function updatedUsd()
    {
        if(!is_numeric($this->bs)){
            $this->bs = 0;
            $this->usd = 0;
            return;
        }


        if($this->usd != ''){
            if($this?->kardex?->id != null){
                $this->bs = $this->usd * $this->kardex->exchange_rate->usd_to_bs;
            }else{
                $this->bs = $this->usd * ExchangeRate::orderBy('id','desc')->first()->usd_to_bs;
            }
            $this->price = $this->usd;
            $this->bs = round($this->bs,2);
            $this->usd = round($this->usd,2);
        }else{
            $this->bs = 0;
            $this->price = 0;
        }
    }

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

    public function mount()
    {
        $this->kardex = new Kardex();
    }
    #[On('getKardex')]
    public function getKardex($id){
        $this->kardex = Kardex::find($id);
        $this->_id = $this->kardex->product_id;
        $this->quantity = $this->kardex->quantity;
        $this->price = $this->kardex->price;
        $this->usd = $this->price;
        $this->updatedUsd();
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
            'total' => 'required|integer|min:'.($this->quantity ?? 0).'|max:'.($this->quantity ?? 0)
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
                        'user_id' => Auth::user()->id,
                        'exchange_rate_id' => ExchangeRate::orderBy('id','desc')->first()->id,
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
        $this->redirect(route('admin.kardex'));
    }


    public function render()
    {
        $heads = [
            'Nombre' => 'name',
            'Tipo' => 'type',
            'Cantidad' => 'quantity',
        ];
        $stores = Store::all();
        $products = Product::where('is_serialize',false)->get();
        return view('livewire.inventory-form',compact('stores','products','heads'));
    }
}
