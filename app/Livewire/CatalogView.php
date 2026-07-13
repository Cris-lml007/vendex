<?php

namespace App\Livewire;

use App\Enums\Status;
use App\Enums\Type;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class CatalogView extends Component
{
    use WithPagination;

    public $list = [
        'search' => '',
        'sort_field' => 'name',
        'sort_direction' => 'asc',
        'pages' => 1
    ];

    public function updatedList(){
        if($this->list['pages'] != ''){
            $this->setPage($this->list['pages']);
        }
    }

    public $product_id;

    public function updatedProductId(){
        $this->js('$("#modal-product").modal("show")');
        $this->getProduct($this->product_id);
    }

    public function getProduct($id)
    {
        $this->dispatch('getProduct', $id)->to(CatalogForm::class);
    }


    public function render()
    {
        $heads = [
            "Nombre" => 'name',
            "Modelo" => 'model',
            "Marca" => 'brand_id',
            "Precio" => 'price',
            "Acciones" => null
        ];

        $search = $this->list['search'];
        if($search != ''){
            $data = Product::where('status',Status::ACTIVE)
                ->where(function($query) use ($search){
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('model', 'like', '%'.$search.'%')
                        ->orWhereHas('brand', function($query) use ($search){
                            $query->where('name', 'like', '%'.$search.'%');
                        })
                        ->orWhere('price', 'like', '%'.$search.'%');
                })
                ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }else {
            $data = Product::where('status', Status::ACTIVE)
                ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }

        $this->list['pages_max'] = $data->lastPage();
        return view('livewire.catalog-view', compact(['data','heads']));
    }
}
