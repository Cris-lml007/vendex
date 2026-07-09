<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Kardex;
use App\Models\Stock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

class InventoryView extends Component
{

    public function getKardex($id){
        $this->dispatch('getKardex',$id)->to(InventoryForm::class);
    }


    public function remove($password, $id){
        if(Hash::check($password, Auth::user()->password)){
            $this->kardex = Kardex::find($id);
            $stock = Stock::where('product_id', $this->kardex->product_id)
                ->where('store_id', $this->kardex->store_id)
                ->first();
            if($this->kardex->type == Type::OUT){
                $stock->quantity -= $this->kardex->quantity;
            }else{
                $stock->quantity += $this->kardex->quantity;
            }
            $stock->save();
            $this->kardex->delete();
            return true;
        }

        return false;
    }

    #[On('refresh')]
    public function render()
    {
        $heads = ['Id', 'Nombre', 'Precio de Adquisición', 'Precio de Venta', 'Cantidad', 'Tipo', 'Locación','Fecha' ,'Acciones'];
        $data = Kardex::all();
        return view('livewire.inventory-view', compact(['heads','data']));
    }
}
