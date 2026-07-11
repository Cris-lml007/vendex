<?php

namespace App\Livewire;

use App\Models\Kardex;
use App\Models\Stock;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class SaleForm extends Component
{
    public $transaction;
    public $customer;
    public $user;

    public $verify = false;

    #[On('getTransaction')]
    public function getTransaction($id){
        $this->transaction = Transaction::find($id);
        $this->customer = $this->transaction->customer;
        $this->user = $this->transaction->user;

        $last = Kardex::latest()->first();
        if($this->transaction->details()->where('id',$last->referenceable->id)->exists()){
            $this->verify = true;
        }
    }

    public function remove()
    {
        try {
            DB::transaction(function () {
                foreach ($this->transaction->details as $detail) {
                    $stock = Stock::where('store_id', $this->transaction->store_id)
                        ->where('product_id', $detail->product_id)
                        ->lockForUpdate()
                        ->first();
                    $stock->quantity += $detail->quantity;
                    $stock->save();
                    $detail->kardex->delete();
                    $detail->delete();
                }
                $this->transaction->delete();
            });

            $this->redirect(route('admin.sales'));
        } catch (\Throwable $e) {
            $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.sale-form');
    }
}
