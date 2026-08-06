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

    public $is_table = true;

    public $list = [
        'search' => '',
        'sort_field' => 'name',
        'sort_direction' => 'asc',
        'pages' => 1
    ];

    public $search;

    public function isTable(){
        $this->is_table = !$this->is_table;
    }

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
        if(Product::where('id', $id)->exists()){
            $this->dispatch('getProduct', $id)->to(CatalogForm::class);
        }else{
            $this->js('Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Producto no encontrado!",
            showConfirmButton: false,
            timer: 1500
            })');
        }
    }


    public function render()
    {
        $heads = [
            'Id' => 'id',
            "Nombre" => 'name',
            'Color' => 'color',
            "Modelo" => 'model',
            "Marca" => 'brand_id',
            "Precio" => 'price',
            "Acciones" => null
        ];

        $search = $this->search;
        if($search != ''){
            $terms = preg_split('/\s+/', trim($search));

            $data = Product::where('status', Status::ACTIVE)
                ->where(function ($query) use ($terms) {

                    foreach ($terms as $term) {

                        $query->where(function ($q) use ($term) {

                            $q->where('id', 'like', "%{$term}%")
                                ->orWhere('name', 'like', "%{$term}%")
                                ->orWhere('model', 'like', "%{$term}%")
                                ->orWhere('price', 'like', "%{$term}%")
                                ->orWhere('color', 'like', "%{$term}%")

                                ->orWhereHas('brand', function ($brand) use ($term) {
                                    $brand->where('name', 'like', "%{$term}%");
                                })

                                ->orWhereHas('tags', function ($tag) use ($term) {
                                    $tag->where('name', 'like', "%{$term}%")
                                        ->orWhere('value', 'like', "%{$term}%");
                                });

                        });

                    }

                })
                ->orderBy($this->list['sort_field'], $this->list['sort_direction'])
                ->paginate();



        }else {
            $data = Product::where('status', Status::ACTIVE)
                ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }

        $this->list['pages_max'] = $data->lastPage();
        $rate = \App\Models\ExchangeRate::orderBy('id','desc')->first()->usd_to_bs;
        return view('livewire.catalog-view', compact(['data','heads','rate']));
    }
}
