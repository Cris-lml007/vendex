<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Enums\Type;
use App\Models\Customer;
use App\Models\DetailTransaction;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SellView extends Component
{
    public $ci;
    public $name;
    public $phone;
    public $email;
    public $customer_id;

    public $product_id;
    public $product_price;
    public $price;
    public $product_quantity;
    public $quantity;
    public $products;
    public $total = 0;

    public $list;
    public $store;
    public $stores;

    public function mount(){
        $this->store = Auth::user()->store->id ?? null;

        if(Auth::user()->store_id == null && Auth::user()->role == Role::SELLER){
            $this->js('Swal.fire({title: "Vaya Ocurrio un Error",text: "consulte con su administrador",icon: "error",showCancelButton: false})');
        }
    }

    public function updatedProductId(){
        $p = Product::find($this->product_id);
        if($p?->id != null){
            $this->product_price = $p->price;
            $this->product_quantity = Stock::where('store_id',$this->store)
                ->where('product_id',$this->product_id)
                ->first()
                ->quantity;
        }else{
            $this->product_price = '';
            $this->product_quantity = '';

        }
    }

    public function updatedCi(){
        $customer = Customer::where('ci',$this->ci)->first();
        if($customer != null){
            $this->name=$customer->name;
            $this->phone=$customer->phone;
            $this->email=$customer->email;
            $this->customer_id=$customer->id;
        }else{
            $this->name = null;
            $this->phone = null;
            $this->email = null;
            $this->customer_id = null;
        }
    }

    public function addProduct(){
        $stock = Stock::where('store_id',$this->store)
            ->where('product_id',$this->product_id)
            ->first();
        if($stock->quantity < $this->quantity){
            $this->js('Swal.fire({title: "Ups...",icon: "error",showCancelButton: false, text: "Parace no haber Unidades Disponibles"})');
            $this->updatedProductId();
            return;
        }

        $product = Product::find($this->product_id);
        $this->list[] = [
            'product_id' => $this->product_id,
            'name' => $product->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];
        $this->total += $this->price * $this->quantity;

        $this->product_id = '';
        $this->product_price = '';
        $this->price = '';
        $this->quantity = '';
        $this->product_quantity = '';
    }

    public function removeProduct($id){
        $this->total -= $this->list[$id]['quantity']*$this->list[$id]['price'];
        array_splice($this->list, $id,1);
    }

    public function save(){
        if(empty($this->list)){
            $this->js('Swal.fire({title: "Vacio?", icon: "warning",showCancelButton: false, text: "Ups...parece que el detalle esta vacio"})');
            return;
        }
        if($this->ci != null || $this->ci != ''){
            $this->validate([
                'name' => 'required',
                'phone' => 'required',
            ]);
            $customer = Customer::updateOrCreate([
                'ci' => $this->ci,
            ], [
                'name' => $this->name,
                'phone' => $this->phone,
                'email' => $this->email,
            ]);
            $this->customer_id = $customer->id ?? null;
        }
        try {
            DB::transaction(function () {
                $transaction = Transaction::create([
                    'customer_id' => $this->customer_id ?? null,
                    'user_id' => Auth::user()->id,
                    'store_id' => $this->store,
                ]);

                foreach ($this->list as $item) {
                    $stock = Stock::where('store_id',$this->store)
                        ->where('product_id',$item['product_id'])
                        ->first();

                    if($stock->quantity < $item['quantity']){
                        throw new \Exception('Cantidad no disponible');
                    }

                    $stock->quantity = $stock->quantity - $item['quantity'];
                    $stock->save();

                    $detail = DetailTransaction::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);

                    $register = Kardex::create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'store_id' => $this->store,
                        'type' => Type::OUT,
                        'user_id' => Auth::user()->id
                    ]);

                    $register->referenceable_type = DetailTransaction::class;
                    $register->referenceable_id = $detail->id;
                    $register->save();
                }

                return $this->redirect(route('admin.sell'));
            });
        } catch (\Throwable $e) {
            #$this->js('Swal.fire({title: "'. addslashes($e->getMessage()).'", icon: "error",showCancelButton: false})');
            dd($e->getMessage());
        }
    }

    public function render()
    {
        $this->products = Product::all();
        $this->stores = Store::all();
        return view('livewire.sell-view');
    }
}
