<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Models\Kardex;
use App\Models\Stock;
use App\Models\Transfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

class TransferForm extends Component
{
    public $user;
    public $date;

    public $details;
    public $_id;
    public $verify;

    #[On('getTransfer')]
    public function getTransfer($id)
    {
        $this->_id = $id;
        $transfer = Transfer::find($id);
        $this->date = $transfer->created_at;
        $this->user = $transfer->user;
        $this->details = $transfer->details;

        $last = Kardex::latest()->first();
        $this->verify = false;
        foreach ($this->details as $detail){
            if($detail->kardex_id == $last->id){
                $this->verify = true;
                break;
            }
        }
    }

    public function remove($password){

        if(Hash::check($password, Auth::user()->password) && Auth::user()->role == Role::ADMIN) {
            $last = Kardex::latest()->first();
            $this->verify = false;
            foreach ($this->details as $detail) {
                if ($detail->kardex_id == $last->id) {
                    $this->verify = true;
                    break;
                }
            }

            if (!$this->verify) {
                $this->js('Swal.fire({icon: "error", title: "Error", text: "No se puede Eliminar", showConfirmButton: false, timer: 3000})');
                return;
            }
            try {
                DB::transaction(function () {
                    foreach ($this->details as $detail) {
                        $stock = Stock::where('product_id', $detail->product_id)
                            ->where('store_id', $detail->store_id)
                            ->lockForUpdate()
                            ->first();

                        $stock->quantity += $detail->quantity - $detail->kardex->quantity;
                        $stock->save();

                        $k = $detail->kardex->id;
                        $detail->delete();
                        Kardex::destroy($k);
                    }
                    Transfer::destroy($this->_id);
                });
            } catch (\Throwable $e) {
                $e->getMessage();
            }

            return $this->redirect(route('admin.transfers'));
        }
    }

    public function render()
    {
        return view('livewire.transfer-form');
    }
}
