<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Kardex;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class StoreForm extends Component
{

    use WithFileUploads;

    public $name;
    public $type;
    public $status;

    public Store $store;
    public $stock;
    public $sales;
    public $edit = false;

    public $address;
    public $phone;
    public $email;

    public $lat;
    public $long;
    public $radius;


    #[Validate('image|max:1024')]
    public $photo;

    public $photo_url;


    public function mount(Store $store = null){
        if($store->id != null){
            $this->edit = true;
            $this->store = $store;
            $this->name = $store->name;
            $this->type = $store->type;
            $this->status = $store->status;
            $this->address = $store->address;
            $this->phone = $store->phone;
            $this->email = $store->email;
            $this->lat = $store->lat;
            $this->long = $store->long;
            $this->radius = $store->radius;

            $this->stock = $this->store->products;
            $this->sales = Kardex::where('store_id', $this->store->id)->where('type',Type::OUT)->get();


            if (Storage::disk('local')->exists("stores/{$this->store->id}.jpg")) {
                $this->photo_url = Storage::disk('local')
                    ->temporaryUrl("stores/{$this->store->id}.jpg",
                        now()->addMinutes(5)
                    );
            }
        }else{
            $this->store = new Store();
        }
    }

    public function save(){
        $this->validate([
            'name' => 'required',
            'type' => 'required',
            'status' => 'required',
            'address' => 'required',
        ]);
        if($this->store->id == null){
            $this->store = new Store();
        }
        $this->store->name = $this->name;
        $this->store->type = $this->type;
        $this->store->status = $this->status;
        $this->store->address = $this->address;
        $this->store->phone = $this->phone;
        $this->store->email = $this->email;
        $this->store->lat = $this->lat;
        $this->store->long = $this->long;
        $this->store->radius = $this->radius;
        $this->store->save();


        if($this->photo != null){
            $this->photo->storeAs('stores', $this->store->id.'.jpg');
        }

        if($this->edit){
            return $this->redirect(route('admin.stores'));
        }
        $this->js('$("#modal-store").modal("hide")');

        $this->dispatch('refresh')->to(StoreView::class);
    }


    public function render()
    {
        $heads = ['Nombre'=> 'name','Disponibles' =>null,'Precio' => null,'Acciones' => null];
        $heads1 = ['Id' => null,'Producto' => null,'Cliente' => null,'Cantidad' => null,'Precio' => null,'Por' => null];
        $this->stock = $this->store->products;
        return view('livewire.store-form',compact('heads','heads1'));
    }
}
