<?php

namespace App\Livewire;

use App\Enums\Currency;
use App\Models\DetailQuotation;
use App\Models\ExchangeRate;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Settings;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class QuotationForm extends Component
{
    public $valid_from;
    public $valid_to;

    public $search = '';

    public $details = [];
    public Settings $settings;

    public function mount()
    {
        $this->settings = Settings::first();
        $this->valid_from = now()->format('Y-m-d');
        $this->valid_to = now()->addDays(7)->format('Y-m-d');
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
            'price' => $product->price,
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
        ]);

        DB::transaction(function () {

            $quotation = Quotation::create([
                'user_id' => auth()->id(),
                'valid_from' => $this->valid_from,
                'valid_to' => $this->valid_to,
            ]);

            foreach ($this->details as $detail) {

                DetailQuotation::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $detail['product_id'],
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ]);
            }
        });

        session()->flash(
            'success',
            'La proforma fue registrada correctamente.'
        );

        return redirect()->route('quotations');
    }

    public function render()
    {
        return view('livewire.quotation-form');
    }
}
