<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProductPrice extends Component
{
    public string $search = '';

    public array $prices = [];

    /**
     * Precios originales para detectar modificaciones.
     */
    public array $originalPrices = [];

    public function mount(): void
    {
        $this->loadPrices();
    }

    /**
     * Cargar los precios actuales de los productos.
     */
    private function loadPrices(): void
    {
        $this->prices = Product::query()
            ->pluck('price', 'id')
            ->map(fn ($price) => number_format((float) $price, 2, '.', ''))
            ->toArray();

        $this->originalPrices = $this->prices;
    }

    /**
     * Normalizar un precio a dos decimales.
     */
    private function normalizePrice($price): string
    {
        return number_format((float) $price, 2, '.', '');
    }

    /**
     * Obtener los productos que tienen precios modificados.
     */
    private function getChangedPrices(): array
    {
        $changed = [];

        foreach ($this->prices as $productId => $price) {
            $originalPrice = $this->originalPrices[$productId] ?? null;

            if ($originalPrice === null) {
                continue;
            }

            if (
                $this->normalizePrice($price) !==
                $this->normalizePrice($originalPrice)
            ) {
                $changed[$productId] = $this->normalizePrice($price);
            }
        }

        return $changed;
    }

    /**
     * Buscar productos por código, nombre, modelo o marca.
     */
    private function productsQuery(): Builder
    {
        return Product::query()
            ->with('brand')
            ->when(trim($this->search) !== '', function (Builder $query) {
                $search = trim($this->search);

                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhereHas('brand', function (Builder $brand) use ($search) {
                            $brand->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('name');
    }

    /**
     * Guardar el precio de un solo producto.
     */
    public function savePrice($productId): void
    {
        $productId = (string) $productId;

        $product = Product::findOrFail($productId);

        $validated = $this->validate([
            "prices.{$productId}" => [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
            ],
        ]);

        $newPrice = $this->normalizePrice(
            $validated['prices'][$productId]
        );

        DB::transaction(function () use ($product, $newPrice) {
            $product->price = $newPrice;
            $product->save();
        });

        $this->prices[$productId] = $newPrice;
        $this->originalPrices[$productId] = $newPrice;

        $this->alert(
            'Precio actualizado',
            "El precio de {$product->name} se actualizó correctamente.",
            'success'
        );
    }

    /**
     * Guardar todos los precios modificados.
     */
    public function saveAll(): void
    {
        $changedPrices = $this->getChangedPrices();

        if (empty($changedPrices)) {
            $this->alert(
                'Sin cambios',
                'No hay precios modificados para guardar.',
                'info'
            );

            return;
        }

        // Validar únicamente los precios que se van a guardar.
        $rules = [];

        foreach ($changedPrices as $productId => $price) {
            $rules["prices.{$productId}"] = [
                'required',
                'numeric',
                'min:0',
                'max:999999999.99',
            ];
        }

        $validated = $this->validate($rules);

        // Volver a obtener los valores validados.
        $changedPrices = [];

        foreach ($rules as $field => $fieldRules) {
            $productId = substr($field, strlen('prices.'));

            $changedPrices[$productId] = $this->normalizePrice(
                $validated['prices'][$productId]
            );
        }

        $count = count($changedPrices);

        DB::transaction(function () use ($changedPrices) {
            foreach ($changedPrices as $productId => $price) {
                Product::query()
                    ->whereKey($productId)
                    ->update([
                        'price' => $price,
                    ]);
            }
        });

        // Actualizar los precios originales tras guardar correctamente.
        foreach ($changedPrices as $productId => $price) {
            $this->prices[$productId] = $price;
            $this->originalPrices[$productId] = $price;
        }

        $this->alert(
            'Precios actualizados',
            "Se actualizaron correctamente {$count} productos.",
            'success'
        );
    }

    /**
     * Mostrar notificación con SweetAlert.
     */
    private function alert(
        string $title,
        string $text,
        string $icon
    ): void {
        $this->js(
            'Swal.fire(' . json_encode([
                'title' => $title,
                'text' => $text,
                'icon' => $icon,
            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ')'
        );
    }

    public function render()
    {
        $products = $this->productsQuery()->get();

        $groupedProducts = $products->groupBy(
            fn (Product $product) => $product->brand?->name ?? 'Sin marca'
        );

        return view('livewire.product-price', [
            'groupedProducts' => $groupedProducts,
            'totalProducts' => $products->count(),
            'modifiedCount' => count($this->getChangedPrices()),
        ]);
    }
}
