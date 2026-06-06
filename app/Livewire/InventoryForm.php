<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InventoryForm extends Component
{

    public $_id;
    public $quantity = 0;
    public $total = 0;
    public $price;

    public $list;

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

    public function save(): void
    {
        $this->validate([
            '_id' => 'required',
            'quantity' => 'required|integer|min:1',
            'price' => 'required',
            'total' => 'required|integer|min:'.$this->quantity.'|max:'.$this->quantity
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
                    ]);

                    Stock::updateOrCreate([
                        'product_id' => $this->_id,
                        'store_id' => $item,
                    ],[
                        'quantity' => $value,
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
        $stores = Store::all();
        $products = Product::all();
        return view('livewire.inventory-form',compact('stores','products'));
    }
}
