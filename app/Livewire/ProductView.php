<?php

namespace App\Livewire;

use App\Enums\Status;
use App\Models\ExchangeRate;
use App\Models\Product;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductView extends Component
{
    use WithPagination;

    public $product_id;

    public $list = [
        'search' => '',
        'sort_field' => 'name',
        'sort_direction' => 'asc',
        'pages' => 1
    ];

    public function updatedProductId()
    {
        $this->dispatch('getBarcode', $this->product_id)->to(ProductForm::class);
        $this->js('$("#modal-scanner").modal("hide");$("#modal-product").modal("show")');
    }

    public function updatedList()
    {
        if($this->list['pages'] != ''){
            $this->setPage($this->list['pages']);
        }
    }

    #[On('refresh')]
    public function render()
    {
        $rate = ExchangeRate::orderBy('id','desc')->first()->usd_to_bs;
        $heads = [
            'ID' => 'id',
            'Nombre' =>'name',
            'Modelo' => 'model',
            'Color' => 'color',
            'Serializado' => 'is_serialize',
            'Marca' => 'brand_id',
            'Categoria' => 'category_id',
            'Precio (Usd)' => 'price',
            'Acciones' => null
        ];

        $search = $this->list['search'];
        if($search != ''){

            $terms = preg_split('/\s+/', trim($search));

            $products = Product::where('status', Status::ACTIVE)
                ->where(function ($query) use ($terms) {

                    foreach ($terms as $term) {

                        $query->where(function ($q) use ($term) {

                            $q->where('id', 'like', "%{$term}%")
                                ->orWhere('name', 'like', "%{$term}%")
                                ->orWhere('model', 'like', "%{$term}%")
                                ->orWhere('price', 'like', "%{$term}%")
                                ->orWhere('color', 'like', "%{$term}%")
                                ->orWhereHas('store', function ($q) use ($term) {
                                    $q->where('name', 'like', "%{$term}%");
                                })

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
        }else{
            $products = Product::whereNot(function($q){
                $q->where('is_serialize',true)->where('parent_id','!=',null);
            })
                ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                ->paginate();
        }
        $this->list['pages_max'] = $products->lastPage();
        //$products = Product::all();
        return view('livewire.product-view',compact(['heads','products','rate']));
    }
}
