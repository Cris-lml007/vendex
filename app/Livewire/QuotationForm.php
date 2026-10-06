<?php

namespace App\Livewire;

use App\Enums\Currency;
use App\Models\DetailQuotation;
use App\Models\ExchangeRate;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Number;
use Livewire\Component;

class QuotationForm extends Component
{
    public $valid_from;
    public $valid_to;

    public $search = '';

    public $details = [];
    public Settings $settings;
    public ?Quotation $quotation = null;

    public function mount(?Quotation $quotation = null)
    {
        $this->settings = Settings::first();

        $this->quotation = $quotation;

        if ($quotation) {

            $this->valid_from = $quotation->valid_from;
            $this->valid_to = $quotation->valid_to;

            $this->details = $quotation->details->map(function ($detail) {

                $product = $detail->product;

                $isUnit = $detail->is_unit;

                return [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $detail->quantity,
                    'price' => Number::format($detail->price,2),

                    'unit' => Number::format($product->price, 2),

                    'wholesale' => Number::format(
                        $product->wholesale_price,
                        2
                    ),

                    'is_unit' => $isUnit,
                ];

            })->toArray();

            return;
        }

        /*
    |--------------------------------------------------------------------------
    | Nueva proforma
    |--------------------------------------------------------------------------
    */

        $this->valid_from = now()->format('Y-m-d');

        $this->valid_to = now()
            ->addDays(7)
            ->format('Y-m-d');
    }




    public function changePrice($id){
        if($this->details[$id]['is_unit']){
            $this->details[$id]['price'] = $this->details[$id]['wholesale'];
            $this->details[$id]['is_unit'] = false;
        }else{
            $this->details[$id]['price'] = $this->details[$id]['unit'];
            $this->details[$id]['is_unit'] = true;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Buscador de productos
    |--------------------------------------------------------------------------
    */

    public function getProductsProperty()
    {
        $terms = preg_split('/\s+/', trim($this->search));

        if ($terms === '') {
            return collect();
        }

        return Product::query()
            ->where(function ($q) use ($terms) {
                foreach ($terms as $term) {
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
                }

            })
            ->with([
                'brand',
                'tags',
            ])
            ->limit(10)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Agregar producto
    |--------------------------------------------------------------------------
    */

    public function addProduct($productId)
    {
        $product = Product::findOrFail($productId);

        /*
        |--------------------------------------------------------------------------
        | Si ya existe, aumentar cantidad
        |--------------------------------------------------------------------------
        */

        foreach ($this->details as $index => $detail) {

            if ((int) $detail['product_id'] === (int) $product->id) {

                $this->details[$index]['quantity']++;

                $this->search = '';

                return;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Nuevo detalle
        |--------------------------------------------------------------------------
        */

        $this->details[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'price' => Number::format($product->price,2),
            'unit' => Number::format($product->price,2),
            'wholesale' => Number::format( $product->wholesale_price,2),
            'is_unit' => true
        ];

        $this->search = '';
    }

    /*
    |--------------------------------------------------------------------------
    | Eliminar producto
    |--------------------------------------------------------------------------
    */

    public function removeProduct($index)
    {
        if (!isset($this->details[$index])) {
            return;
        }

        unset($this->details[$index]);

        $this->details = array_values($this->details);
    }

    /*
    |--------------------------------------------------------------------------
    | Total
    |--------------------------------------------------------------------------
    */

    public function getTotalProperty()
    {
        return collect($this->details)->sum(function ($detail) {

            return (float) ($detail['quantity'] ?? 0)
                * (float) ($detail['price'] ?? 0);

        });
    }

    /*
    |--------------------------------------------------------------------------
    | Guardar
    |--------------------------------------------------------------------------
    */

    public function save()
    {
        $this->validate([
            'valid_from' => [
                'required',
                'date',
            ],

            'valid_to' => [
                'required',
                'date',
                'after_or_equal:valid_from',
            ],

            'details' => [
                'required',
                'array',
                'min:1',
            ],

            'details.*.product_id' => [
                'required',
                'exists:products,id',
            ],

            'details.*.quantity' => [
                'required',
                'numeric',
                'min:1',
            ],

            'details.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ], attributes: [
                'details.*.quantity' => 'cantidades',
                'details.*.price' => 'precios',
            ]);

        DB::transaction(function () {

            /*
        |--------------------------------------------------------------------------
        | Crear o recuperar proforma
        |--------------------------------------------------------------------------
        */

            if ($this->quotation) {

                $quotation = $this->quotation;

                $quotation->update([
                    'valid_from' => $this->valid_from,
                    'valid_to' => $this->valid_to,
                ]);

                /*
            |--------------------------------------------------------------------------
            | Eliminamos los detalles anteriores
            |--------------------------------------------------------------------------
            */

                $quotation->details()->delete();

            } else {

                $quotation = Quotation::create([
                    'user_id' => auth()->id(),
                    'valid_from' => $this->valid_from,
                    'valid_to' => $this->valid_to,
                ]);
            }


            /*
        |--------------------------------------------------------------------------
        | Guardar detalles
        |--------------------------------------------------------------------------
        */

            foreach ($this->details as $detail) {

                DetailQuotation::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                    'is_unit' => $detail['is_unit']
                ]);
            }
        });

        session()->flash(
            'success',
            $this->quotation
            ? 'La proforma fue actualizada correctamente.'
            : 'La proforma fue registrada correctamente.'
        );



                $this->js("
    Swal.fire({
        title: '¡Proforma Generada!',
        text: '¿Desea imprimir la Proforma?',
        icon: 'success',

        confirmButtonText: 'Nueva Proforma',
        denyButtonText: 'Imprimir Proforma',

        showDenyButton: true,
        allowOutsideClick: false,
        allowEscapeKey: false

    }).then((result) => {

        if (result.isConfirmed) {

            window.location.replace('" .route('admin.quotations') ."');

        } else if (result.isDenied) {

            window.open('".route('admin.quotation.id.pdf',$this->quotation->id)."', '_blank');

            setTimeout(() => {
                window.location.reload();
            }, 500);

        }

    });
");
        // return redirect()->route('admin.quotations');
    }

    public function render()
    {
        return view('livewire.quotation-form');
    }
}
