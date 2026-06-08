<?php

namespace App\Livewire;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
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

    public function updatedProductId(){
        $p = Product::find($this->product_id);
        $this->product_price = $p->price;
        $this->product_quantity = Stock::where('store_id','')
            ->where('product_id',$this->product_id)
            ->first()
            ->pivot
            ->quantity;
    }

    public function updatedCi(){
        $customer = Customer::where('ci',$this->ci)->first();
        if($customer != null){
            $this->name=$customer->name;
            $this->phone=$customer->phone;
            $this->email=$customer->email;
            $this->customer_id=$customer->id;
        }
    }

    public function addProduct(){
        $product = Product::find($this->product_id);
        $this->list[] = [
            'product_id' => $this->product_id,
            'name' => $product->name,
            'quantity' => $this->quantity,
            'price' => $this->price,
        ];
        $this->total += $this->price * $this->quantity;
    }

    public function removeProduct($id){

    }

    public function save(){

    }

    public function render()
    {
        $this->products = Product::all();
        return view('livewire.sell-view');
    }
}
