<?php

namespace App\Livewire;

use App\Enums\Status;
use App\Enums\Type;
use App\Models\ExchangeRate;
use App\Models\Kardex;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportStock extends Component
{
    use WithFileUploads;

    public $file;

    public array $sheetNames = [];
    public ?string $selectedSheet = null;

    public array $headers = [];
    public array $preview = [];
    public array $rows = [];

    public int $totalRows = 0;
    public int $step = 1;

    public array $mapping = [
        'stock' => [
            'product_id' => '',
            'store' => '',
            'quantity' => '',
            'purchase_price' => '',
        ],
    ];

    public array $mappedData = [
        'stocks' => [],
    ];

    /*
    |--------------------------------------------------------------------------
    | Tiendas nuevas
    |--------------------------------------------------------------------------
    |
    | Cada elemento representa una tienda única encontrada en el Excel.
    |
    */
    public array $newStores = [];

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    public function updatedFile()
    {
        $this->resetImportData();

        if (!$this->file) {
            return;
        }

        $this->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $this->loadSheets();
    }

    protected function loadSheets()
    {
        $spreadsheet = IOFactory::load(
            $this->file->getRealPath()
        );

        $this->sheetNames = $spreadsheet->getSheetNames();

        if (count($this->sheetNames) === 1) {
            $this->selectedSheet = $this->sheetNames[0];

            $this->loadSelectedSheet();
        }
    }

    public function updatedSelectedSheet()
    {
        if ($this->selectedSheet) {
            $this->loadSelectedSheet();
        }
    }

    protected function loadSelectedSheet()
    {
        if (!$this->file || !$this->selectedSheet) {
            return;
        }

        $spreadsheet = IOFactory::load(
            $this->file->getRealPath()
        );

        $sheet = $spreadsheet->getSheetByName(
            $this->selectedSheet
        );

        if (!$sheet) {
            return;
        }

        $data = $sheet->toArray(
            null,
            true,
            true,
            false
        );

        if (empty($data)) {
            return;
        }

        $this->headers = array_map(
            fn ($header) => trim((string) $header),
            $data[0]
        );

        $this->rows = [];

        foreach (array_slice($data, 1) as $row) {
            $hasData = collect($row)->contains(
                fn ($value) => trim((string) $value) !== ''
            );

            if ($hasData) {
                $this->rows[] = $row;
            }
        }

        $this->totalRows = count($this->rows);

        $this->preview = array_slice(
            $this->rows,
            0,
            10
        );

        $this->autoMap();

        $this->step = 2;
    }

    /*
    |--------------------------------------------------------------------------
    | Mapping
    |--------------------------------------------------------------------------
    */

    protected function autoMap()
    {
        $aliases = [
            'product_id' => [
                'id',
                'producto',
                'producto id',
                'id producto',
                'product id',
                'codigo',
                'cod',
                'codigo producto',
            ],

            'store' => [
                'tienda',
                'tienda nombre',
                'nombre tienda',
                'almacen',
                'almacén',
                'nombre almacen',
                'nombre almacén',
                'store',
            ],

            'quantity' => [
                'cantidad',
                'stock',
                'existencia',
                'existencias',
                'quantity',
            ],

            'purchase_price' => [
                'precio compra',
                'precio de compra',
                'precio_compra',
                'purchase price',
                'purchase_price',
            ],
        ];

        foreach ($aliases as $field => $names) {

            $this->mapping['stock'][$field] = '';

            foreach ($this->headers as $index => $header) {

                $normalized = $this->normalizeHeader(
                    $header
                );

                if (in_array($normalized, $names)) {
                    $this->mapping['stock'][$field] = (string) $index;
                    break;
                }
            }
        }
    }

    protected function normalizeHeader($value): string
    {
        $value = mb_strtolower(
            trim((string) $value)
        );

        $value = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'u'],
            $value
        );

        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Preview import
    |--------------------------------------------------------------------------
    */

    public function previewImport()
    {
        $this->validate([
            'mapping.stock.product_id' => 'required',
            'mapping.stock.store' => 'required',
            'mapping.stock.quantity' => 'required',
        ]);

        $this->transformData();

        /*
         * Importante:
         * Las tiendas se detectan sobre TODAS las filas,
         * incluso aquellas cuyo producto no existe.
         */
        $this->detectNewStores();

        $this->step = 3;
    }

    protected function transformData()
    {
        $this->mappedData['stocks'] = [];

        foreach ($this->rows as $row) {
            $this->mappedData['stocks'][] = $this->mapStock($row);
        }
    }

    protected function mapStock(array $row): array
    {
        $productId = trim(
            (string) $this->value(
                $row,
                $this->mapping['stock']['product_id']
            )
        );

        $store = trim(
            (string) $this->value(
                $row,
                $this->mapping['stock']['store']
            )
        );

        $quantity = trim(
            (string) $this->value(
                $row,
                $this->mapping['stock']['quantity']
            )
        );

        $purchasePrice = trim(
            (string) $this->value(
                $row,
                $this->mapping['stock']['purchase_price']
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Producto
        |--------------------------------------------------------------------------
        */

        if ($productId === '') {
            return [
                'product_id' => '',
                'product_name' => '',
                'store' => $store,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'valid' => false,
                'reason' => 'No se indicó el ID del producto.',
            ];
        }

        $product = Product::find($productId);

        if (!$product) {
            return [
                'product_id' => $productId,
                'product_name' => '',
                'store' => $store,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'valid' => false,
                'reason' => 'El producto no existe en Vendex.',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Tienda
        |--------------------------------------------------------------------------
        */

        if ($store === '') {
            return [
                'product_id' => $productId,
                'product_name' => $product->name,
                'store' => '',
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'valid' => false,
                'reason' => 'No se indicó la tienda o almacén.',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Cantidad
        |--------------------------------------------------------------------------
        */

        if ($quantity === '') {
            return [
                'product_id' => $productId,
                'product_name' => $product->name,
                'store' => $store,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'valid' => false,
                'reason' => 'No se indicó la cantidad.',
            ];
        }

        if (!is_numeric($quantity)) {
            return [
                'product_id' => $productId,
                'product_name' => $product->name,
                'store' => $store,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'valid' => false,
                'reason' => 'La cantidad no es numérica.',
            ];
        }

        if ((float) $quantity < 0) {
            return [
                'product_id' => $productId,
                'product_name' => $product->name,
                'store' => $store,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'valid' => false,
                'reason' => 'La cantidad no puede ser negativa.',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Precio de compra
        |--------------------------------------------------------------------------
        */

        if (
            $purchasePrice !== '' &&
            !is_numeric($purchasePrice)
        ) {
            return [
                'product_id' => $productId,
                'product_name' => $product->name,
                'store' => $store,
                'quantity' => $quantity,
                'purchase_price' => $purchasePrice,
                'valid' => false,
                'reason' => 'El precio de compra no es numérico.',
            ];
        }

        return [
            'product_id' => $productId,
            'product_name' => $product->name,
            'store' => $store,
            'quantity' => (float) $quantity,
            'purchase_price' => $purchasePrice !== ''
                ? (float) $purchasePrice
                : null,
            'valid' => true,
            'reason' => '',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Tiendas nuevas
    |--------------------------------------------------------------------------
    */

    protected function detectNewStores()
    {
        $this->newStores = [];

        /*
         * No usamos únicamente las filas válidas.
         *
         * Una fila puede tener un producto inexistente pero
         * igualmente revelar una tienda nueva.
         */
        foreach ($this->mappedData['stocks'] as $stock) {

            $storeName = trim(
                (string) ($stock['store'] ?? '')
            );

            if ($storeName === '') {
                continue;
            }

            /*
             * Si la tienda ya existe en Vendex,
             * no necesitamos pedir sus datos.
             */
            $exists = Store::where(
                'name',
                $storeName
            )->exists();

            if ($exists) {
                continue;
            }

            /*
             * Evitar duplicados ignorando mayúsculas,
             * minúsculas y espacios.
             */
            $alreadyAdded = collect($this->newStores)
                ->contains(function ($store) use ($storeName) {

                    return mb_strtolower(
                        trim($store['name'])
                    ) === mb_strtolower(
                        $storeName
                    );
                });

            if ($alreadyAdded) {
                continue;
            }

            $this->newStores[] = [
                'name' => $storeName,
                'type' => '',
                'status' => Status::ACTIVE,
                'address' => '',
                'phone' => '',
                'email' => '',
            ];
        }
    }

    public function isNewStore(string $name): bool
    {
        $name = mb_strtolower(
            trim($name)
        );

        if ($name === '') {
            return false;
        }

        return collect($this->newStores)
            ->contains(function ($store) use ($name) {

                return mb_strtolower(
                    trim($store['name'])
                ) === $name;
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Import
    |--------------------------------------------------------------------------
    */

    public function import()
    {
        /*
         * Validar tiendas nuevas antes de comenzar
         * cualquier modificación en la base de datos.
         */
        foreach ($this->newStores as $index => $store) {

            if (trim($store['name'] ?? '') === '') {
                $this->addError(
                    "newStores.$index.name",
                    'El nombre de la tienda es obligatorio.'
                );

                return;
            }

            if (trim($store['type'] ?? '') === '') {
                $this->addError(
                    "newStores.$index.type",
                    'El tipo de tienda es obligatorio.'
                );

                return;
            }

            if (trim($store['status'] ?? '') === '') {
                $this->addError(
                    "newStores.$index.status",
                    'El estado es obligatorio.'
                );

                return;
            }

            if (trim($store['address'] ?? '') === '') {
                $this->addError(
                    "newStores.$index.address",
                    'La dirección es obligatoria.'
                );

                return;
            }

            if (
                trim($store['email'] ?? '') !== '' &&
                !filter_var(
                    trim($store['email']),
                    FILTER_VALIDATE_EMAIL
                )
            ) {
                $this->addError(
                    "newStores.$index.email",
                    'El correo electrónico no es válido.'
                );

                return;
            }
        }

        $validRows = collect(
            $this->mappedData['stocks']
        )->filter(
            fn ($stock) => $stock['valid'] === true
        );

        if ($validRows->isEmpty()) {
            $this->addError(
                'import',
                'No existen registros válidos para importar.'
            );

            return;
        }

        try {

            DB::transaction(function () use ($validRows) {

                /*
                |--------------------------------------------------------------------------
                | Crear tiendas
                |--------------------------------------------------------------------------
                */

                foreach ($this->newStores as $storeData) {

                    Store::firstOrCreate(
                        [
                            'name' => trim(
                                $storeData['name']
                            ),
                        ],
                        [
                            'type' => $storeData['type'],
                            'status' => $storeData['status'],
                            'address' => trim(
                                $storeData['address']
                            ),
                            'phone' => trim(
                                $storeData['phone'] ?? ''
                            ) !== ''
                                ? trim($storeData['phone'])
                                : null,
                            'email' => trim(
                                $storeData['email'] ?? ''
                            ) !== ''
                                ? trim($storeData['email'])
                                : null,
                        ]
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Importar stock
                |--------------------------------------------------------------------------
                */

                foreach ($validRows as $stockData) {

                    /*
                     * Volvemos a comprobar el producto dentro
                     * de la transacción.
                     */
                    $product = Product::find(
                        $stockData['product_id']
                    );

                    if (!$product) {
                        continue;
                    }

                    $store = Store::where(
                        'name',
                        trim($stockData['store'])
                    )->first();

                    /*
                     * No debería ocurrir porque las tiendas nuevas
                     * ya fueron creadas, pero mantenemos esta
                     * protección.
                     */
                    if (!$store) {
                        continue;
                    }

                    $stock = Stock::firstOrNew([
                        'product_id' => $product->id,
                        'store_id' => $store->id,
                    ]);

                    /*
                     * El valor del Excel representa el stock final.
                     */
                    $stock->quantity = ($stock->quantity ?? 0) + $stockData['quantity'];

                    /*
                     * Solo asignamos precio si fue proporcionado.
                     *
                     * Si tu tabla stocks no tiene purchase_price,
                     * elimina este bloque.
                     */

                    $stock->save();

                    $k = Kardex::create([
                        'product_id' => $product->id,
                        'store_id' => $stock->store_id,
                        'quantity' => $stockData['quantity'],
                        'price' => $stockData['purchase_price'],
                        'type' => Type::IN,
                        'user_id' => Auth::user()->id,
                        'exchange_rate_id' => ExchangeRate::orderBy('id','desc')->first()->id

                    ]);
                }
            });

            $this->step = 4;

            session()->flash(
                'success',
                'El stock se importó correctamente.'
            );

        } catch (\Throwable $e) {

            report($e);

            dd($e->getMessage());
            $this->addError(
                'import',
                'No se pudo completar la importación. No se realizaron cambios.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    protected function value(
        array $row,
        $column
    ) {
        if (
            $column === '' ||
            $column === null
        ) {
            return '';
        }

        return $row[(int) $column] ?? '';
    }

    public function resetImportData()
    {
        $this->sheetNames = [];
        $this->selectedSheet = null;

        $this->headers = [];
        $this->preview = [];
        $this->rows = [];

        $this->totalRows = 0;
        $this->step = 1;

        $this->mapping = [
            'stock' => [
                'product_id' => '',
                'store' => '',
                'quantity' => '',
                'purchase_price' => '',
            ],
        ];

        $this->mappedData = [
            'stocks' => [],
        ];

        $this->newStores = [];

        $this->resetErrorBag();
    }

    public function restart()
    {
        $this->file = null;

        $this->resetImportData();
    }

    public function render()
    {
        return view(
            'livewire.import-stock'
        );
    }
}
