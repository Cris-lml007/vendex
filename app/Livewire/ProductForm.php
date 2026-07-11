<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DetailTransfer;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\ProductSequense;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductForm extends Component
{

    public $name;
    public $price;
    public $category;
    public $description;
    public $brand;
    public $model;
    public $barcode;

    public Product $product;

    public $edit = false;

    public $stores;
    public $stocks;
    public $total = 0;
    public $total_origin = 0;

    public function setStock($id, $stock){
        $this->total = $this->total - $this->stocks[$id];
        $this->stocks[$id] = $stock;
        $this->total = $this->total + $stock;
    }

    public function mount(Product $product = null){
        $this->product = $product;
        if($this->product->id != null){
            $this->edit = true;
            $this->barcode = $this->product->id;
            $this->name = $this->product->name;
            $this->price = $this->product->price;
            $this->category = $this->product->category_id;
            $this->description = $this->product->description;
            $this->barcode = $this->product->id;
            $this->brand = $this->product->brand_id;
            $this->model = $this->product->model;

            $this->stores = Store::all();
            foreach ($this->stores as $store){
                $this->stocks[$store->id] = $store->products()->where('product_id',$product->id)->first()?->pivot?->quantity ?? 0;
                $this->total = $this->total + $this->stocks[$store->id];
                $this->total_origin = $this->total_origin + $this->stocks[$store->id];
            }
        }else{
            $this->product = new Product();
        }
    }

    public function save(){
        if($this->product->id == null){
            $this->product = new Product();
        }
        $this->product->name = $this->name;
        $this->product->price = $this->price;
        $this->product->description = $this->description;
        $this->product->brand_id = $this->brand;
        $this->product->model = $this->model;
        $this->product->category_id = $this->category;
        if($this->barcode != ''){
            $this->product->id = $this->barcode;
        }else{
            $p = ProductSequense::create();
            $this->product->id = str_pad($p->id, 10, "0", STR_PAD_LEFT);
        }
        $this->product->save();
        if($this->edit){
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
                                'user_id' => Auth::user()->id
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
                });
            }catch(\Throwable $exception){
                dd($exception);
            }
            return $this->redirect(route('admin.products'));
        }

        $this->reset();
        $this->js('$("#modal-product").modal("hide")');
        $this->dispatch('refresh')->to(ProductView::class);
    }

    public function render()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('livewire.product-form')->with('categories',$categories)->with('brands',$brands);
    }
}
