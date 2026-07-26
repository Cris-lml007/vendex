<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Models\Brand;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

class StoreView extends Component
{
    public function remove($password, $id)
    {
        if(Hash::check($password, Auth::user()->password) && Auth::user()->role == Role::ADMIN){
            $store = Store::find($id);
            if($store->transactions()->count() > 0 || $store->products()->count() > 0 || $store->products_serial()->count() > 0){
                return false;
            }
            Store::destroy($id);
            return true;
        }else{
            return false;
        }
    }
    #[On('refresh')]
    public function render()
    {
        $stores = Store::all();
        $heads = [
            'Nombre' => 'name',
            'Tipo' => 'type',
            'Estado' => 'state',
            'Acciones' => null
        ];
        return view('livewire.store-view', compact(['heads','stores']));
    }
}
