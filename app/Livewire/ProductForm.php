<?php

namespace App\Livewire;

use App\Enums\Status;
use App\Enums\Type;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DetailTransfer;
use App\Models\ExchangeRate;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\ProductSequense;
use App\Models\Stock;
use App\Models\Store;
use App\Models\TagProduct;
use App\Models\Transfer;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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

    #[Validate('image|max:1024')]
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

    public $min_quantity = [];

    public $bs;
    public $usd;
    public $bs1;
    public $usd1;

    public $rate;

    public $search;

    public $color;

    public function updatedSearch(){
    }


    public function updatedBs(){

        if(!is_numeric($this->bs)){
            $this->bs = 0;
            $this->usd = 0;
            return;
        }
        if($this->bs != ''){
            $this->usd = $this->bs / ExchangeRate::orderBy('id','desc')->first()->usd_to_bs;
            $this->price = $this->usd;
            $this->usd = round($this->usd,2);
        }else{
            $this->usd = 0;
            $this->price = 0;
        }
    }

    public function updatedUsd()
    {
        if(!is_numeric($this->usd)){
            $this->bs = 0;
            $this->usd = 0;
            return;
        }
        if($this->usd != ''){
            $this->bs = $this->usd * ExchangeRate::orderBy('id','desc')->first()->usd_to_bs;
            $this->price = $this->usd;
            $this->bs = round($this->bs,2);
            $this->usd = round($this->usd,2);
        }else{
            $this->bs = 0;
            $this->price = 0;
        }
    }

    public function updatedBs1(){
        if(!is_numeric($this->bs)){
            $this->bs = 0;
            $this->usd = 0;
            return;
        }
        if($this->bs1 != ''){
            if($this->edit){
                $this->usd1 = $this->bs1 / $this->product->kardex()->first()->exchange_rate->usd_to_bs;
            }else{
                $this->usd1 = $this->bs1 / ExchangeRate::orderBy('id','desc')->first()->usd_to_bs;
            }
            $this->price_purchase = $this->usd1;
            $this->usd1 = round($this->usd1,2);
        }else{
            $this->usd1 = 0;
            $this->price_purchase = 0;
        }
    }

    public function updatedUsd1()
    {
        if(!is_numeric($this->usd)){
            $this->bs = 0;
            $this->usd = 0;
            return;
        }
        if($this->usd1 != ''){
            if($this->edit){
                $this->bs1 = $this->usd1 * $this->product->kardex()->first()->exchange_rate->usd_to_bs;
            }else{
                $this->bs1 = $this->usd1 * ExchangeRate::orderBy('id','desc')->first()->usd_to_bs;
            }
            $this->price_purchase = $this->usd1;
            $this->bs1 = round($this->bs1,2);
            $this->usd1 = round($this->usd1,2);
        }else{
            $this->bs1 = 0;
            $this->price_purchase = 0;
        }
    }

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
            $this->usd = $this->price;
            $this->updatedUsd();
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
            $this->usd = 0;
            $this->updatedUsd();
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
        $this->stocks[$id] = $stock == '' ? 0 : $stock;
        $this->total = $this->total + ($stock == '' ? 0 : $stock);
    }

    public function setMinQuantity($id, $quantity)
    {
        $this->min_quantity[$id] = $quantity;
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
            'barcodes-'.$this->product->id.'.pdf'
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
            'Color' => 'color',
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
            $this->color = $this->product->color ?? '';
            $this->usd = $this->product->price;
            $this->updatedUsd();

            $this->price_purchase = $this->product?->kardex()?->first()?->price ?? null;
            $this->usd1 = $this->price_purchase;
            $this->updatedUsd1();
            $this->store_id = $this->product?->store_id ?? null;

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
                //$this->photo_url = Storage::disk('local')->get("products/{$this->product->id}.jpg");

                $this->photo_url = Storage::disk('local')
                    ->temporaryUrl("products/{$this->product->id}.jpg",
                        now()->addMinutes(5)
                    );

                //$this->photo_url = "data:image/png;base64,". base64_encode($this->photo_url);
            }
            $v = false;
            $p = $this?->product?->parent;
            do{
                if($p?->id != null){
                    if(Storage::disk('local')->exists("products/{$p->id}.jpg")) {
                        $this->photo_url = Storage::disk('local')
                            ->temporaryUrl("products/{$p->id}.jpg",
                                now()->addMinutes(5)
                            );
                        $v = true;
                    }
                }else{
                    $v = true;
                }
                $p = $p?->parent ?? null;
            }while(!$v);

            $this->stores = Store::all();
            foreach ($this->stores as $store){
                $this->stocks[$store->id] = $store->products()->where('product_id',$product->id)->first()?->pivot?->quantity ?? 0;
                $this->min_quantity[$store->id] = $store->stocks()->where('product_id',$product->id)->first()?->min_quantity ?? 0;
                $this->total = $this->total + $this->stocks[$store->id];
                $this->total_origin = $this->total_origin + $this->stocks[$store->id];
            }
        }else{
            $this->product = new Product();
            //dd($this->product);
        }
    }

    public function remove()
    {
        if($this->product->id != null){
            if( $this->product->details()->count() < 1 && $this->product->kardex()->count() < 1){
                Stock::where('product_id',$this->product->id)->delete();
                Storage::disk('local')->delete("products/{$this->product->id}.jpg");
                $this->product->delete();
                $this->redirect(route('admin.products'));
            }
        }
    }

    public function save(){
        //dd($this->product_serials);
        //dd($this->store_id);
        //dd($this->photo);
        $r = [
            'name' => 'required',
            'price' => 'required|numeric|min:0',
            //'category' => 'required',
            //'brand' => 'required',
            'model' => 'required',
            'barcode' => 'unique:products,id,'. $this?->product?->id ?? '',
        ];

        if($this->is_serial == 1){
            $this->price_purchase = $this->usd1;
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

        for ($i = 0; $i < $this->number_labels ;$i++){
            if(empty($this->labels) || empty($this->values)){
                return $this->js('Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Etiqueta Vacia!",
            showConfirmButton: false,
            timer: 1500
            })');
            }

            if(!isset($this?->labels[$i]) || !isset($this?->values[$i])){

                return $this->js('Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Etiqueta Vacia!",
            showConfirmButton: false,
            timer: 1500
            })');
            }
        }

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
            $this->product->color = $this->color;
            if($this->barcode != ''){
                $this->product->id = $this->barcode;
            }else{
                $p = ProductSequense::create();
                $this->product->id = str_pad($p->id, 10, "0", STR_PAD_LEFT);
            }

            $this->product->is_serialize = $this->is_serial;
            $this->product->parent_id = $this->product_id;
            #$this->product->store_id = $this->store_id;
            //dd($this->store_id, $this->product->store_id);
            if($this->store_id != $this->product->store_id && $this->edit) {
                $t = new Transfer();
                $t->user_id = Auth::id();
                $t->save();

                $k1 = Kardex::create([
                    'product_id' => $this->product->id,
                    'store_id' => $this->product->store_id,
                    'quantity' => 0,
                    'price' => 0,
                    'type' => Type::TRANSFER,
                    'user_id' => Auth::user()->id,
                    'exchange_rate_id' => ExchangeRate::orderBy('id', 'desc')->first()->id,
                ]);
                $k2 = Kardex::create([
                    'product_id' => $this->product->id,
                    'store_id' => $this->store_id,
                    'quantity' => 1,
                    'price' => 0,
                    'type' => Type::TRANSFER,
                    'user_id' => Auth::user()->id,
                    'exchange_rate_id' => ExchangeRate::orderBy('id', 'desc')->first()->id,
                ]);
                $transfer = DetailTransfer::create([
                    'transfer_id' => $t->id,
                    'product_id' => $this->product->id,
                    'store_id' => $this->product->store_id,
                    'quantity' => 1,
                    'kardex_id' => $k1->id,
                ]);
                $transfer = DetailTransfer::create([
                    'transfer_id' => $t->id,
                    'product_id' => $this->product->id,
                    'store_id' => $this->store_id,
                    'quantity' => 0,
                    'kardex_id' => $k2->id,
                ]);
            }
            $this->product->store_id = $this->store_id ?? null;
            $this->product->save();

            if(!$this->edit){
                if($this->product->is_serialize){
                    Kardex::create([
                        'product_id' => $this->product->id,
                        'quantity' => 1,
                        'price' => $this->price_purchase,
                        'type' => Type::IN,
                        'user_id' => auth()->id(),
                        'store_id' => $this->store_id,
                        'exchange_rate_id' => ExchangeRate::orderBy('id', 'desc')->first()->id,
                    ]);
                }
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

            try{
                DB::transaction(function () {
                    foreach ($this->min_quantity as $id => $value){
                        Stock::updateOrCreate([
                            'product_id' => $this->product->id,
                            'store_id' => $id,
                        ],[
                            'min_quantity' => $value
                        ]);
                    }

                    foreach ($this->product->children ?? [] as $child){
                        if($child->is_serialize){
                            $child->price = $this->price;
                            $child->name = $this->name;
                            $child->description = $this->description;
                            $child->brand_id = $this->brand;
                            $child->model = $this->model;
                            $child->category_id = $this->category;
                            $child->save();
                        }
                    }
                });
            }catch (\Exception $exception){
                dd($exception);
            }

            $q = $this->product?->stocks()->sum('quantity') ?? 0;
            if($q > 0 || !empty($this->product_serials)){
                if(array_sum($this->stocks) != $q){
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
                                    'user_id' => Auth::user()->id,
                                    'exchange_rate_id' => ExchangeRate::orderBy('id', 'desc')->first()->id,
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
                                'user_id' => Auth::user()->id,
                                'exchange_rate_id' => ExchangeRate::orderBy('id', 'desc')->first()->id,
                            ]);
                            $k2 = Kardex::create([
                                'product_id' => $kk->product_id,
                                'store_id' => $value,
                                'quantity' => 1,
                                'price' => 0,
                                'type' => Type::TRANSFER,
                                'user_id' => Auth::user()->id,
                                'exchange_rate_id' => ExchangeRate::orderBy('id', 'desc')->first()->id,
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
        #$this->js('$("#modal-product").modal("hide")');
        #$this->dispatch('refresh')->to(ProductView::class);
        $this->redirect(route('admin.products'));
    }

    public function render()
    {
        $categories = Category::all();
        $brands = Brand::all();
        if($this->search != ''){

            $terms = preg_split('/\s+/', trim($this->search));

            $products = Product::where('status', Status::ACTIVE)
                ->where('is_serialize',false)
                ->where(function ($query) use ($terms) {

                    foreach ($terms as $term) {

                        $query->where(function ($q) use ($term) {

                            $q->where('name', 'like', "%{$term}%")
                                ->orWhere('id', 'like', "%{$term}%")
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

                })->get();
        }else{
            $products = Product::where('is_serialize',false)->get();
        }
        return view('livewire.product-form')
            ->with('categories',$categories)
            ->with('brands',$brands)
            ->with('products',$products);
    }
}
