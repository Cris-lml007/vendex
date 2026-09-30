<?php

namespace App\Livewire;

use App\Enums\Currency;
use App\Enums\Status;
use App\Enums\Type;
use App\Models\Product;
use App\Models\Settings;
use App\Models\TagProduct;
use Livewire\Component;
use Livewire\WithPagination;

class CatalogView extends Component
{
    use WithPagination;

    public $is_table = true;
    public Settings $settings;

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
        $this->settings = Settings::first();
        $heads = [
            'ID' => 'id',
            'Nombre' =>'name',
            'Modelo' => 'model',
        ];

        $this->settings = Settings::first();

        foreach ($this->settings->product_tags ?? [] as $value) {
            $heads [$value] = $value;
        }

        $heads ['Color'] = 'color';
        $heads ['Serializado'] = 'is_serialize';
        $heads ['Marca'] = 'brand_id';
        $heads ['Categoria'] = null;
        $heads ['Cantidad'] = null;
        $heads ['Precio ('.($this->settings->currency_main == Currency::BS ? 'Bs' : 'Usd').')'] = 'price';
        $heads ['Acciones'] = null;

        $sortField = $this->list['sort_field'];
        $sortDirection = $this->list['sort_direction'];

        $productTags = $this->settings->product_tags ?? [];
        $isTag = in_array($sortField, $productTags);


        if($this->is_table){
            $search = $this->list['search'];
        }else{
            $search = $this->search;
        }
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

                });
                // ->orderBy($this->list['sort_field'], $this->list['sort_direction'])
                // ->paginate();



        }else {
            $data = Product::where('status', Status::ACTIVE);
                // ->orderBy($this->list['sort_field'],$this->list['sort_direction'])
                // ->paginate();
        }


        if ($isTag) {

            $data = $data
                ->orderBy(
                    TagProduct::select('value')
                        ->whereColumn('tag_products.product_id', 'products.id')
                        ->where('name', $sortField)
                        ->limit(1),
                    $sortDirection
                )->paginate();

        } else {

            $data = $data
                ->orderBy($sortField, $sortDirection)->paginate();

        }

        $this->list['pages_max'] = $data->lastPage();
        $rate = \App\Models\ExchangeRate::orderBy('id','desc')->first()->usd_to_bs;
        return view('livewire.catalog-view', compact(['data','heads','rate']));
    }
}
