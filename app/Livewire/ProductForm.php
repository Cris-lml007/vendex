<?php

namespace App\Livewire;

use App\Enums\Status;
use App\Enums\Type;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DetailTransfer;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\ProductSequense;
use App\Models\Stock;
use App\Models\Store;
use App\Models\TagProduct;
use App\Models\Transfer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Milon\Barcode\Facades\DNS1DFacade;

class ProductForm extends Component
{
    use WithFileUploads;

    public $name;
    public $price;
    public $category;
    public $description;
    public $brand;
    public $model;
    public $barcode;

    #[Validate('image|max:2048')]
    public $photo;

    public $photo_url;

    public Product $product;

    public $edit = false;

    public $stores;
    public $stocks;
    public $total = 0;
    public $total_origin = 0;

    public $barcode_img;

    public $tags;

    public $labels = [];
    public $values = [];
    public $number_labels = 0;

    public $is_serial = false;
    public $product_id;

    public $heads = [];
    public $price_purchase;

    public $store_id;
    public $stores_list;

    public $product_serials = [];

    public function updatedIsSerial()
    {
        if($this->is_serial == 0){
            $this->name = '';
            $this->barcode = '';
            $this->price = '';
            $this->description = '';
            $this->category = '';
            $this->brand = '';
            $this->model = '';
            $this->barcode_img = '';
            $this->labels = [];
            $this->values = [];
            $this->number_labels = 0;
            $this->product_id = '';
        }
    }

    public function updatedProductId(){
        if($this->product_id != ''){
            $p = Product::find($this->product_id);
            $this->name = $p->name;
            $this->price = $p->price;
            $this->category = $p->category_id;
            $this->description = $p->description;
            $this->brand = $p->brand_id;
            $this->model = $p->model;
            $this->values = $p->tags()->pluck('value')->toArray();
            $this->labels = $p->tags()->pluck('name')->toArray();
            $this->number_labels = $p->tags()->count() ?? 0;
        }else{
            $this->name = '';
            $this->barcode = '';
            $this->price = '';
            $this->description = '';
            $this->category = '';
            $this->brand = '';
            $this->model = '';
            $this->barcode_img = '';
            $this->labels = [];
            $this->values = [];
            $this->number_labels = 0;
            $this->product_id = '';
        }
    }

    public function addLabel(){
        $this->number_labels++;
    }

    public function removeTag($index)
    {
        $this->number_labels--;
        array_splice($this->labels, $index, 1);
        array_splice($this->values, $index, 1);
    }

    public function setStock($id, $stock){
        $this->total = $this->total - $this->stocks[$id];
        $this->stocks[$id] = $stock;
        $this->total = $this->total + $stock;
    }

    #[On('getBarcode')]
    public function getBarcode($value)
    {
        $this->barcode = $value;
        $this->updatedBarcode();
    }

    public function updatedBarcode(){
        $this->barcode_img = $this->generateBarcode($this->barcode);
    }

    public function generatePdf(){
        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ])->loadView('pdf.barcodes', [
            'name' => $this->product->name,
            'barcode' => $this->generateBarcode($this->barcode,2,33),
            'tags' => $this->tags
        ]);

        $pdf->setPaper('A4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'barcodes-'.$this->product->name.'-'.$this->tags .'.pdf'
        );
    }

    public function generateBarcode($value, $w = 5, $h = 55){
        if($value == ''){
            return '';
        }else{
            return 'data:image/png;base64,' .
                DNS1DFacade::getBarcodePNG($value, 'C128', $w,$h, array(1,1,1), true);
        }
    }

    public function mount($id = null){
        $this->heads = [
            'Id' => 'id',
            'Nombre' => 'name',
            'Precio' => 'price',
            'locación' => 'location',
        ];
        $this->stores_list = Store::where('status', Status::ACTIVE)->get();

        $product = Product::find($id);
        $this->product = $product ?? new Product();
        if($this->product->id != null){
            $this->edit = true;
            $this->barcode = $this->product->id;
            $this->name = $this->product->name;
            $this->price = $this->product->price;
            $this->category = $this->product->category_id;
            $this->description = $this->product->description;
            #$this->barcode = $this->product->id;
            $this->brand = $this->product->brand_id;
            $this->model = $this->product->model;
            $this->number_labels = $this->product->tags()->count() ?? 0;
            $this->values = $this->product->tags()->pluck('value')->toArray();
            $this->labels = $this->product->tags()->pluck('name')->toArray();
            $this->is_serial = $this->product->is_serialize;
            $this->product_id = $this->product->parent_id;

            $this->barcode_img = $this->generateBarcode($this->barcode);//  'data:image/png;base64,' . DNS1DFacade::getBarcodePNG($this->barcode, 'C128');

            if (Storage::disk('local')->exists("products/{$this->product->id}.jpg")) {
                $this->photo_url = Storage::disk('local')->get("products/{$this->product->id}.jpg");
                $this->photo_url = "data:image/png;base64,". base64_encode($this->photo_url);
            }
            $this->stores = Store::all();
            foreach ($this->stores as $store){
                $this->stocks[$store->id] = $store->products()->where('product_id',$product->id)->first()?->pivot?->quantity ?? 0;
                $this->total = $this->total + $this->stocks[$store->id];
                $this->total_origin = $this->total_origin + $this->stocks[$store->id];
            }
        }else{
            $this->product = new Product();
            //dd($this->product);
        }
    }

    public function save(){
        //dd($this->product_serials);
        //dd($this->store_id);
        $r = [
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            'category' => 'required',
            'brand' => 'required',
            'model' => 'required',
            'description' => 'required',
            'barcode' => 'unique:products,id,'. $this?->product?->id ?? '',
        ];

        if($this->is_serial == "true"){
            $r['price_purchase'] = 'required|numeric|min:0';
            $r['store_id'] = 'required';
        }else{
            unset($r['price_purchase']);
            unset($r['store_id']);
        }

        $this->validate($r, attributes: [
            'category' => 'Categoria',
            'brand' => 'Marca',
            'model' => 'Modelo',
            'barcode' => 'Codigo de Barras',
            'price_purchase' => 'Precio de compra',
        ]);

        try {
            if($this->product->id == null){
                $this->product = new Product();
            }
            $this->product->name = $this->name;
            $this->product->price = $this->price;
            $this->product->description = $this->description;
            $this->product->brand_id = $this->brand;
            $this->product->model = $this->model;
            $this->product->category_id = $this->category;
            if($this->barcode != ''){
                $this->product->id = $this->barcode;
            }else{
                $p = ProductSequense::create();
                $this->product->id = str_pad($p->id, 10, "0", STR_PAD_LEFT);
            }

            $this->product->is_serialize = $this->is_serial;
            $this->product->parent_id = $this->product_id;
            $this->product->store_id = $this->store_id ?? null;
            $this->product->save();

            if($this->product->is_serialize){
                Kardex::create([
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'price' => $this->price_purchase,
                    'type' => Type::IN,
                    'user_id' => auth()->id(),
                    'store_id' => $this->store_id,
                ]);
            }

            if($this->number_labels > 0){
                DB::transaction(function () {
                    for ($i = 0;$i < $this->number_labels ;$i++){
                        TagProduct::create([
                            'product_id' => $this->product->id,
                            'name' => $this->labels[$i],
                            'value' => $this->values[$i],
                        ]);
                    }
                });
            }

        }catch (\Exception $exception){
            dd($exception);
        }

        if($this->photo != null){
            $this->photo->storeAs('products', $this->product->id.'.jpg');
        }

        if($this->edit){
            $q = $this->product?->stocks()->sum('quantity') ?? 0;
            if($q > 0 || !empty($this->product_serials)){
                if(array_sum($this->stocks) > $q || (array_sum($this->stocks) == 0 && $this->product->stocks()->sum('quantity') > 0) ){
                    $this->js('Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Unidades no Disponibles"
                })');
                    return;
                }
                try{
                    DB::transaction(function(){
                        $t = new Transfer();
                        $t->user_id = Auth::id();
                        $t->save();
                        foreach ($this->stocks as $id => $value){
                            $stock = Stock::where('product_id',$this->product->id)
                                ->where('store_id',$id)
                                ->first();
                            if(($stock?->quantity ?? 0) != $value){
                                $quantity_old = 0;
                                if($stock?->id != null){
                                    $quantity_old = $stock->quantity;
                                }

                                $kardex = Kardex::create([
                                    'product_id' => $this->product->id,
                                    'store_id' => $id,
                                    'quantity' => $value,
                                    'price' => 0,
                                    'type' => Type::TRANSFER,
                                    'user_id' => Auth::user()->id
                                ]);
                                Stock::updateOrCreate([
                                    'product_id' => $this->product->id,
                                    'store_id' => $id,
                                ],[
                                    'quantity' => $value,
                                ]);

                                $transfer = DetailTransfer::create([
                                    'transfer_id' => $t->id,
                                    'product_id' => $this->product->id,
                                    'store_id' => $id,
                                    'quantity' => $quantity_old ?? 0,
                                    'kardex_id' => $kardex->id,
                                ]);
                            }
                        }

                        foreach ($this->product_serials as $id => $value){
                            $kk = Kardex::find($id);

                            $kk->product->update([
                                'store_id' => $value,
                            ]);

                            $k1 = Kardex::create([
                                'product_id' => $kk->product_id,
                                'store_id' => $kk->store_id,
                                'quantity' => 0,
                                'price' => 0,
                                'type' => Type::TRANSFER,
                                'user_id' => Auth::user()->id
                            ]);
                            $k2 = Kardex::create([
                                'product_id' => $kk->product_id,
                                'store_id' => $value,
                                'quantity' => 1,
                                'price' => 0,
                                'type' => Type::TRANSFER,
                                'user_id' => Auth::user()->id
                            ]);
                            $transfer = DetailTransfer::create([
                                'transfer_id' => $t->id,
                                'product_id' => $kk->product_id,
                                'store_id' => $kk->store_id,
                                'quantity' => 1,
                                'kardex_id' => $k1->id,
                            ]);
                            $transfer = DetailTransfer::create([
                                'transfer_id' => $t->id,
                                'product_id' => $kk->product_id,
                                'store_id' => $value,
                                'quantity' => 0,
                                'kardex_id' => $k2->id,
                            ]);
                        }

                        if($t->details()->count() == 0){
                            $t->delete();
                        }
                    });
                }catch(\Throwable $exception){
                    dd($exception);
                }
            }
            return $this->redirect(route('admin.products'));
        }

        $this->reset();
        $this->js('$("#modal-product").modal("hide")');
        $this->dispatch('refresh')->to(ProductView::class);
    }

    public function render()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $products = Product::where('parent_id',null)->get();
        return view('livewire.product-form')
            ->with('categories',$categories)
            ->with('brands',$brands)
            ->with('products',$products);
    }
}
