<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Enums\Status;
use App\Enums\Type;
use App\Models\Customer;
use App\Models\DetailTransaction;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
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

    public Transaction $transaction;

    public $search;
    public $is_search = false;

    public function searchOn()
    {
        $this->is_search = !$this->is_search;
    }

    public function mount(){
        $this->store = Auth::user()->store->id ?? null;

        if(Auth::user()->store_id == null && Auth::user()->role == Role::SELLER){
            $this->js('Swal.fire({title: "Vaya Ocurrio un Error",text: "consulte con su administrador",icon: "error",showCancelButton: false})');
        }
    }

    public function updatedStore(){
        if($this->product_id != ''){
            $this->updatedProductId();
        }
    }

    public function updatedProductId(){
        $p = Product::find($this->product_id);
        if($p?->id != null && $p->status == Status::ACTIVE){
            if($this->store == ''){
                $this->product_price = '';
                $this->product_quantity = '';
                $this->quantity = '';
                $this->price = '';

                $this->js('Swal.fire({
            title: "Sin Tienda?",
            text: "Por favor Seleccione una Tienda",
            icon: "warning"})
            ');
                return;
            }
            $this->product_price = $p->price;

            $p = Product::find($this->product_id);
            if($p->is_serialize && $p->status == Status::ACTIVE){
                $this->product_quantity = 1;
            }else{
                $this->product_quantity = Stock::where('store_id',$this->store)
                    ->where('product_id',$this->product_id)
                    ->first()
                    ->quantity ?? 0;
            }
        }else{
            $this->product_price = '';
            $this->product_quantity = '';
            $this->js('Swal.fire({
            title: "Upss...",
            text: "Parece que el producto no existe",
            icon: "warning"})
            ');
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
        if($this->product_id == '' || $this->quantity == '' || $this->quantity < 1 || $this->price < 1 || $this->price == ''){
            $this->js('Swal.fire({
            title: "Sin Productos?",
            text: "Por favor Seleccione un Producto",
            icon: "warning"
            })');
            return;
        }

        $p = Product::find($this->product_id);
        if($p->is_serialize && $p->status == Status::ACTIVE){
            $value = 1;
        }else{
            $value = Stock::where('store_id',$this->store)
                ->where('product_id',$this->product_id)
                ->first()->quantity ?? 0;
        }
        if($value < $this->quantity){
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
                    $p = Product::find($item['product_id']);
                    $stock = null;
                    if($p->is_serialize && $p->status == Status::ACTIVE){
                        $value = 1;
                    }else{
                        $stock = Stock::where('store_id',$this->store)
                            ->where('product_id',$item['product_id'])
                            ->first();
                        $value =  $stock->quantity ?? 0;
                    }

                    if($value < $item['quantity']){
                        throw new \Exception('Cantidad no disponible');
                    }

                    if($stock != null){
                        $stock->quantity = $stock->quantity - $item['quantity'];
                        $stock->save();
                    }

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
                    $p->store_id = null;
                    $p->status = Status::SALE;
                    $p->save();
                }

                $this->transaction = $transaction;

                $this->js("
    Swal.fire({
        title: '¡Venta realizada exitosamente!',
        text: '¿Desea imprimir el recibo?',
        icon: 'success',

        confirmButtonText: 'Nueva Venta',
        denyButtonText: 'Imprimir Recibo',

        showDenyButton: true,
        allowOutsideClick: false,
        allowEscapeKey: false

    }).then((result) => {

        if (result.isConfirmed) {

            window.location.reload();

        } else if (result.isDenied) {

            window.open('".route('admin.sell.id', $transaction->id)."', '_blank');

            setTimeout(() => {
                window.location.reload();
            }, 500);

        }

    });
");
                //return $this->redirect(route('admin.sell'));
            });
        } catch (\Throwable $e) {
            #$this->js('Swal.fire({title: "'. addslashes($e->getMessage()).'", icon: "error",showCancelButton: false})');
            dd($e->getMessage());
        }
    }

    public function generateReceipt(){
    }

    public function render()
    {
        if(Auth::user()?->store?->status != Status::ACTIVE && Auth::user()->role == Role::SELLER){
            return abort(403);
        }

        if($this->search != ''){
            $terms = preg_split('/\s+/', trim($this->search));

            $this->products = Product::where('status', Status::ACTIVE)
                ->where(function ($query) {
                    $query->whereHas('stocks', function($q){
                        $q->where('store_id',$this->store);
                    })->orWhere(function($qq){
                        $qq->where('store_id',$this->store)
                            ->where('store_id','!=',null);
                    });
                })
                ->where(function ($query) use ($terms) {

                    foreach ($terms as $term) {

                        $query->where(function ($q) use ($term) {

                            $q->where('name', 'like', "%{$term}%")
                                ->orWhere('id', 'like', "%{$term}%")
                                ->orWhere('model', 'like', "%{$term}%")
                                ->orWhere('price', 'like', "%{$term}%")

                                ->orWhereHas('brand', function ($brand) use ($term) {
                                    $brand->where('name', 'like', "%{$term}%");
                                })

                                ->orWhereHas('tags', function ($tag) use ($term) {
                                    $tag->where('name', 'like', "%{$term}%")
                                        ->orWhere('value', 'like', "%{$term}%");
                                });

                        });

                    }

                })->get();
        }else{
            $this->products = Product::where('status', Status::ACTIVE)
                ->where(function ($query) {
                    $query->whereHas('stocks', function($q){
                        $q->where('store_id',$this->store);
                    })->orWhere(function($qq){
                        $qq->where('store_id',$this->store)
                            ->where('store_id','!=',null);
                    });
                })->get();
        }

        $this->stores = Store::where('type',Type::STORE)
            ->where('status', Status::ACTIVE)->get();
        return view('livewire.sell-view');
    }
}
