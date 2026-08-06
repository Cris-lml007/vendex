<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\DetailTransfer;
use App\Models\ExchangeRate;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Transfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Livewire\Attributes\On;
use Livewire\Component;

class CatalogForm extends Component
{

    public $name;
    public $price;
    public $category;
    public $description;
    public $brand;
    public $model;
    public $product;
    public $store_id;
    public $color;


    public $stores;
    public $stocks;
    public $stocks_cp;
    public $total = 0;
    public $total_origin = 0;

    #[On('getProduct')]
    public function getProduct($id)
    {
        $product = Product::find($id);
        $this->name = $product->name;
        $this->price = Number::format($product->price* \App\Models\ExchangeRate::orderBy('id','desc')->first()->usd_to_bs,2);
        $this->category = $product->category?->name ?? '';
        $this->description = $product->description;
        $this->brand = $product->brand?->name ?? '';
        $this->model = $product->model;
        $this->store_id = $product->store_id;
        $this->color = $product->color ?? '';

        $this->product = $product;


        $this->stores = Store::where('type',Type::STORE)->get();
        foreach ($this->stores as $store){
            $this->stocks[$store->id] = $store->products()->where('product_id',$product->id)->first()?->pivot?->quantity ?? 0;
            $this->total = $this->total + $this->stocks[$store->id];
            $this->total_origin = $this->total;//$this->total_origin + $this->stocks[$store->id];
        }
        $this->stocks_cp = $this->stocks;
    }


    public function setStock($id, $stock){
        $this->total = $this->total - $this->stocks[$id];
        $this->stocks[$id] = $stock == '' ? 0 : $stock;
        $this->total = $this->total + ($stock == '' ? 0 : $stock);
    }

    public function transfer(){

        $q = $this->product?->stocks()->sum('quantity') ?? 0;
        if($q > 0 && !$this->product->is_serialize){
            if(array_sum($this->stocks) > $q || (array_sum($this->stocks) == 0 && $this->product->stocks()->sum('quantity') > 0) ){
                $this->js('Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Unidades no Disponibles"
                })');
                return;
            }
            try{
                DB::transaction(function(){
                    $t = new Transfer();
                    $t->user_id = Auth::id();
                    $t->save();
                    foreach ($this->stocks as $id => $value){
                        $stock = Stock::where('product_id',$this->product->id)
                            ->where('store_id',$id)
                            ->first();
                        if(($stock?->quantity ?? 0) != $value){
                            $quantity_old = 0;
                            if($stock?->id != null){
                                $quantity_old = $stock->quantity;
                            }

                            $kardex = Kardex::create([
                                'product_id' => $this->product->id,
                                'store_id' => $id,
                                'quantity' => $value,
                                'price' => 0,
                                'type' => Type::TRANSFER,
                                'user_id' => Auth::user()->id,
                                'exchange_rate_id' => ExchangeRate::orderBy('id', 'desc')->first()->id,
                            ]);
                            Stock::updateOrCreate([
                                'product_id' => $this->product->id,
                                'store_id' => $id,
                            ],[
                                'quantity' => $value,
                            ]);

                            $transfer = DetailTransfer::create([
                                'transfer_id' => $t->id,
                                'product_id' => $this->product->id,
                                'store_id' => $id,
                                'quantity' => $quantity_old ?? 0,
                                'kardex_id' => $kardex->id,
                            ]);
                        }
                    }
                    if($t->details()->count() == 0){
                        $t->delete();
                    }
                    $this->redirect(route('admin.catalog'));
                });
            }catch(\Throwable $exception){
                dd($exception);
            }
        }else if($this->product->is_serialize){

            $t = new Transfer();
            $t->user_id = Auth::id();
            $t->save();

            $k1 = Kardex::create([
                'product_id' => $this->product->id,
                'store_id' => $this->product->store_id,
                'quantity' => 0,
                'price' => 0,
                'type' => Type::TRANSFER,
                'user_id' => Auth::user()->id,
                'exchange_rate_id' => ExchangeRate::orderBy('id', 'desc')->first()->id,
            ]);
            $k2 = Kardex::create([
                'product_id' => $this->product->id,
                'store_id' => $this->store_id,
                'quantity' => 1,
                'price' => 0,
                'type' => Type::TRANSFER,
                'user_id' => Auth::user()->id,
                'exchange_rate_id' => ExchangeRate::orderBy('id', 'desc')->first()->id,
            ]);
            $transfer = DetailTransfer::create([
                'transfer_id' => $t->id,
                'product_id' => $this->product->id,
                'store_id' => $this->product->store_id,
                'quantity' => 1,
                'kardex_id' => $k1->id,
            ]);
            $transfer = DetailTransfer::create([
                'transfer_id' => $t->id,
                'product_id' => $this->product->id,
                'store_id' => $this->store_id,
                'quantity' => 0,
                'kardex_id' => $k2->id,
            ]);
            $this->product->store_id = $this->store_id ?? null;
            $this->product->save();
            $this->redirect(route('admin.catalog'));
        }
    }

    public function render()
    {
        return view('livewire.catalog-form');
    }
}
