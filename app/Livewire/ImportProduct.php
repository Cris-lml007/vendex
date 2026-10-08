<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportProduct extends Component
{
    use WithFileUploads;

    public $file;

    public array $sheetNames = [];

    public int $selectedSheet = 0;

    public array $headers = [];

    public array $preview = [];

    public array $rows = [];

    public int $totalRows = 0;

    public int $step = 1;

    public array $mapping = [
        'product' => [
            'id' => '',
            'name' => '',
            'model' => '',
            'description' => '',
            'price' => '',
            'color' => '',
            'parent_id' => '',
            'wholesale_price' => '',
            'category' => '',
            'brand' => '',
            'tags' => [],
        ],

        'category' => [
            'name' => '',
        ],

        'brand' => [
            'name' => '',
            'made_type' => 'fixed',
            'made' => '',
            'bg_color_type' => 'fixed',
            'bg_color' => '',
            'fb_color_type' => 'fixed',
            'fb_color' => '',
        ],
    ];

    public array $mappedData = [
        'products' => [],
        'categories' => [],
        'brands' => [],
    ];

    public $spreadsheet;

    /*
    |--------------------------------------------------------------------------
    | Upload
    |--------------------------------------------------------------------------
    */

    public function updatedFile()
    {
        $this->resetValidation();

        $this->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:10240',
            ],
        ]);

        $this->resetImportData();

        try {

            $this->loadSheets();

            $this->selectedSheet = 0;

            $this->loadSelectedSheet();

            $this->autoMap();

            $this->step = 1;

        } catch (\Throwable $e) {

            $this->addError(
                'file',
                'No se pudo leer el archivo: ' .
                    $e->getMessage()
            );
        }
    }

protected function loadSheets(): void
{
    $path = $this->file->getRealPath();

    dump([
        'path' => $path,
        'exists' => file_exists($path),
        'size' => filesize($path),
    ]);

    dump('ANTES DE IOFACTORY');

    $spreadsheet = IOFactory::load($path);

    dump('DESPUÉS DE IOFACTORY');

    $this->sheetNames = $spreadsheet->getSheetNames();

    dump($this->sheetNames);
}

    public function updatedSelectedSheet()
    {
        if (!$this->file) {
            return;
        }

        $this->loadSelectedSheet();

        $this->autoMap();
    }

    protected function loadSelectedSheet(): void
    {
        // $spreadsheet = IOFactory::load(
        //     $this->file->getRealPath()
        // );

        $sheet = $this->spreadsheet->getSheet(
            $this->selectedSheet
        );

        $data = $sheet->toArray(
            null,
            true,
            true,
            false
        );

        if (empty($data)) {

            $this->headers = [];
            $this->rows = [];
            $this->preview = [];
            $this->totalRows = 0;

            return;
        }

        $this->headers = array_map(
            fn ($header) =>
            trim((string) $header),
            $data[0]
        );

        unset($data[0]);

        $data = array_values(
            array_filter(
                $data,
                function ($row) {

                    foreach ($row as $value) {

                        if (
                            $value !== null &&
                                trim((string) $value) !== ''
                        ) {
                            return true;
                        }
                    }

                    return false;
                }
            )
        );

        $this->rows = $data;

        $this->totalRows =
        count($data);

        $this->preview =
        array_slice(
            $data,
            0,
            10
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Mapping
    |--------------------------------------------------------------------------
    */

    protected function autoMap(): void
    {
        $normalizedHeaders = [];

        foreach (
        $this->headers as $index => $header
    ) {
            $normalizedHeaders[$index] =
            $this->normalizeHeader(
                $header
            );
        }

        $aliases = [

            'id' => [
                'id',
                'codigo',
                'código',
                'cod',
                'codigo producto',
                'código producto',
                'product id',
            ],

            'name' => [
                'name',
                'nombre',
                'producto',
                'nombre producto',
                'product name',
            ],

            'model' => [
                'model',
                'modelo',
            ],

            'description' => [
                'description',
                'descripcion',
                'descripción',
            ],

            'price' => [
                'price',
                'precio',
                'precio venta',
                'precio de venta',
                'price sale',
            ],

            'color' => [
                'color',
            ],

            'parent_id' => [
                'parent id',
                'parent_id',
                'id padre',
                'codigo padre',
                'código padre',
            ],

            'wholesale_price' => [
                'wholesale price',
                'precio mayorista',
                'precio por mayor',
                'precio mayor',
            ],

            'category' => [
                'category',
                'categoria',
                'categoría',
                'category name',
                'nombre categoria',
                'nombre categoría',
            ],

            'brand' => [
                'brand',
                'marca',
                'brand name',
                'nombre marca',
            ],

            'category_name' => [
                'category name',
                'nombre categoria',
                'nombre categoría',
            ],

            'brand_name' => [
                'brand name',
                'nombre marca',
            ],

            'made' => [
                'made',
                'fabricado',
                'fabricante',
            ],

            'bg_color' => [
                'bg color',
                'background color',
                'color fondo',
            ],

            'fb_color' => [
                'fb color',
                'foreground color',
                'color texto',
            ],
        ];

        $findHeader = function ($field) use (
        $aliases,
        $normalizedHeaders
    ) {
            if (!isset($aliases[$field])) {
                return '';
            }

            foreach (
            $aliases[$field] as $alias
        ) {

                $alias =
                $this->normalizeHeader(
                    $alias
                );

                foreach (
                $normalizedHeaders
                as $index => $header
            ) {

                    if (
                        $header === $alias
                    ) {
                        return (string) $index;
                    }
                }
            }

            return '';
        };

        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        foreach (
        [
            'id',
            'name',
            'model',
            'description',
            'price',
            'color',
            'parent_id',
            'wholesale_price',
            'category',
            'brand',
        ] as $field
    ) {

            $this->mapping['product'][$field] =
            $findHeader($field);
        }

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        $this->mapping['category']['name'] = '';

        /*
        |--------------------------------------------------------------------------
        | Brands
        |--------------------------------------------------------------------------
        */

        $this->mapping['brand']['name'] = '';

        $this->mapping['brand']['made'] =
        $findHeader('made');

        $this->mapping['brand']['bg_color'] =
        $findHeader('bg_color');

        $this->mapping['brand']['fb_color'] =
        $findHeader('fb_color');

        if (
            $this->mapping['brand']['made']
        ) {
            $this->mapping['brand']['made_type'] =
            'excel';
        }

        if (
            $this->mapping['brand']['bg_color']
        ) {
            $this->mapping['brand']['bg_color_type'] =
            'excel';
        }

        if (
            $this->mapping['brand']['fb_color']
        ) {
            $this->mapping['brand']['fb_color_type'] =
            'excel';
        }
    }

    protected function normalizeHeader($value): string
    {
        $value = Str::ascii(
            mb_strtolower(
                trim((string) $value)
            )
        );

        $value = str_replace(
            ['_', '-'],
            ' ',
            $value
        );

        return preg_replace(
            '/\s+/',
            ' ',
            $value
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Active sections
    |--------------------------------------------------------------------------
    */

    protected function sectionHasMapping(
        string $section
    ): bool {

        $mapping =
        $this->mapping[$section] ?? [];

        foreach (
        $mapping as $key => $value
    ) {

            if ($key === 'tags') {

                if (!empty($value)) {
                    return true;
                }

                continue;
            }

            if (
                str_ends_with(
                    $key,
                    '_type'
                )
            ) {
                continue;
            }

            if (
                $value !== null &&
                    $value !== ''
            ) {
                return true;
            }
        }

        return false;
    }

    public function hasMapping(
        string $section
    ): bool {
        return $this->sectionHasMapping(
            $section
        );
    }

    protected function getActiveSections(): array
    {
        $sections = [];

        foreach (
        [
            'product',
            'category',
            'brand',
        ] as $section
    ) {

            if (
                $this->sectionHasMapping(
                    $section
                )
            ) {
                $sections[] = $section;
            }
        }

        return $sections;
    }

    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    public function previewImport()
    {
        $this->resetValidation();

        if (empty($this->rows)) {

            $this->addError(
                'file',
                'El archivo no contiene datos.'
            );

            return;
        }

        if (
            empty(
                $this->getActiveSections()
            )
        ) {

            $this->addError(
                'mapping',
                'Debes mapear al menos un campo.'
            );

            return;
        }

        $this->transformData();

        $this->validateImportPreview();

        if (
            count(
                $this->getErrorBag()->all()
            ) > 0
        ) {
            return;
        }

        $this->step = 3;
    }

    /*
    |--------------------------------------------------------------------------
    | Transform
    |--------------------------------------------------------------------------
    */

    protected function transformData(): void
    {
        $this->mappedData = [
            'products' => [],
            'categories' => [],
            'brands' => [],
        ];

        foreach (
        $this->rows as $row
    ) {

            if (
                $this->sectionHasMapping(
                    'product'
                )
            ) {

                $product =
                $this->mapProduct($row);

                if (
                    !empty($product['id']) ||
                        !empty($product['name'])
                ) {

                    $this->mappedData['products'][] =
                    $product;
                }
            }

            if (
                $this->sectionHasMapping(
                    'category'
                )
            ) {

                $category =
                $this->mapCategory($row);

                if (
                    !empty($category['name'])
                ) {

                    $exists = collect(
                        $this->mappedData['categories']
                    )->contains(
                            fn ($item) =>
                            mb_strtolower(
                                $item['name']
                            ) ===
                                mb_strtolower(
                                    $category['name']
                                )
                        );

                    if (!$exists) {

                        $this->mappedData['categories'][] =
                        $category;
                    }
                }
            }

            if (
                $this->sectionHasMapping(
                    'brand'
                )
            ) {

                $brand =
                $this->mapBrand($row);

                if (
                    !empty($brand['name'])
                ) {

                    $exists = collect(
                        $this->mappedData['brands']
                    )->contains(
                            fn ($item) =>
                            mb_strtolower(
                                $item['name']
                            ) ===
                                mb_strtolower(
                                    $brand['name']
                                )
                        );

                    if (!$exists) {

                        $this->mappedData['brands'][] =
                        $brand;
                    }
                }
            }
        }
    }

    protected function value(
        array $row,
        $column
    ): string {

        if (
            $column === '' ||
                $column === null
        ) {
            return '';
        }

        return trim(
            (string) (
                $row[(int) $column] ?? ''
            )
        );
    }

    protected function mapProduct(
        array $row
    ): array {

        return [

            'id' =>
            $this->value(
                $row,
                $this->mapping['product']['id']
            ),

            'name' =>
            $this->value(
                $row,
                $this->mapping['product']['name']
            ),

            'model' =>
            $this->value(
                $row,
                $this->mapping['product']['model']
            ),

            'description' =>
            $this->value(
                $row,
                $this->mapping['product']['description']
            ),

            'price' =>
            $this->value(
                $row,
                $this->mapping['product']['price']
            ),

            'color' =>
            $this->value(
                $row,
                $this->mapping['product']['color']
            ),

            'parent_id' =>
            $this->value(
                $row,
                $this->mapping['product']['parent_id']
            ),

            'wholesale_price' =>
            $this->value(
                $row,
                $this->mapping['product']['wholesale_price']
            ),

            'category' =>
            $this->value(
                $row,
                $this->mapping['product']['category']
            ),

            'brand' =>
            $this->value(
                $row,
                $this->mapping['product']['brand']
            ),

            'tags' =>
            $this->mapTags($row),
        ];
    }

    protected function mapTags(array $row): array
    {
        $tags = [];

        foreach ($this->mapping['product']['tags'] ?? [] as $column) {
            if ($column === '' || $column === null) {
                continue;
            }

            $value = $this->value($row, $column);

            if ($value !== '') {
                $tags[] = [
                    'name' => $this->headers[(int) $column] ?? 'Tag',
                    'value' => $value,
                ];
            }
        }

        return $tags;
    }

    protected function mapCategory(
        array $row
    ): array {

        return [
            'name' =>
            $this->value(
                $row,
                $this->mapping['category']['name']
            ),
        ];
    }

    protected function mapBrand(
        array $row
    ): array {

        return [

            'name' =>
            $this->value(
                $row,
                $this->mapping['brand']['name']
            ),

            'made' =>
            $this->mapping['brand']['made_type'] === 'excel'
            ? $this->value(
                $row,
                $this->mapping['brand']['made']
            )
            : $this->mapping['brand']['made'],

            'bg_color' =>
            $this->mapping['brand']['bg_color_type'] === 'excel'
            ? $this->value(
                $row,
                $this->mapping['brand']['bg_color']
            )
            : $this->mapping['brand']['bg_color'],

            'fb_color' =>
            $this->mapping['brand']['fb_color_type'] === 'excel'
            ? $this->value(
                $row,
                $this->mapping['brand']['fb_color']
            )
            : $this->mapping['brand']['fb_color'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected function validateImportPreview(): void
    {
        if (
            $this->sectionHasMapping(
                'product'
            )
        ) {

            foreach (
            $this->mappedData['products']
            as $index => $product
        ) {

                if (
                    empty($product['id']) &&
                        empty($product['name'])
                ) {

                    $this->addError(
                        "products.$index",
                        'El producto de la fila ' .
                            ($index + 2) .
                            ' no tiene código ni nombre.'
                    );
                }

                if (
                    $product['price'] !== '' &&
                        !is_numeric(
                            $product['price']
                        )
                ) {

                    $this->addError(
                        "products.$index",
                        'El precio del producto de la fila ' .
                            ($index + 2) .
                            ' no es válido.'
                    );
                }

                if (
                    $product['wholesale_price'] !== '' &&
                        !is_numeric(
                            $product['wholesale_price']
                        )
                ) {

                    $this->addError(
                        "products.$index",
                        'El precio mayorista del producto de la fila ' .
                            ($index + 2) .
                            ' no es válido.'
                    );
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Import
    |--------------------------------------------------------------------------
    */

    public function import()
    {
        $this->resetValidation();

        if (
            empty(
                $this->getActiveSections()
            )
        ) {

            $this->addError(
                'mapping',
                'No hay ninguna sección activa.'
            );

            return;
        }

        try {

            DB::transaction(
                function () {

                    /*
                    |--------------------------------------------------------------------------
                    | Categories
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $this->sectionHasMapping(
                            'category'
                        )
                    ) {

                        foreach (
                        $this->mappedData['categories']
                        as $category
                    ) {

                            Category::firstOrCreate([
                                'name' =>
                                trim(
                                    $category['name']
                                ),
                            ]);
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Brands
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $this->sectionHasMapping(
                            'brand'
                        )
                    ) {

                        foreach (
                        $this->mappedData['brands']
                        as $brand
                    ) {

                            $this->findOrCreateBrand(
                                $brand['name'],
                                $brand['made'],
                                $brand['bg_color'],
                                $brand['fb_color']
                            );
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Products
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $this->sectionHasMapping(
                            'product'
                        )
                    ) {

                        foreach (
                        $this->mappedData['products']
                        as $productData
                    ) {

                            $product =
                            $this->resolveProduct(
                                $productData
                            );

                            $this->fillProduct(
                                $product,
                                $productData
                            );

                            $product->save();

                            $this->importProductTags(
                                $product,
                                $productData['tags'] ?? []
                            );
                        }
                    }
                }
            );

            $this->step = 4;

            session()->flash(
                'success',
                'La importación se realizó correctamente.'
            );

        } catch (\Throwable $e) {

            $this->addError(
                'import',
                'No se pudo completar la importación: ' .
                    $e->getMessage()
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Product resolution
    |--------------------------------------------------------------------------
    */

    protected function resolveProduct(
        array $data
    ): Product {

        if (
            !empty($data['id'])
        ) {
            $product =
            Product::find(
                $data['id']
            );

            if ($product) {
                return $product;
            }

            $product =  new Product();
            $product->id = $data['id'];
            return $product;
        }

        if (
            !empty($data['name'])
        ) {

            $query =
            Product::where(
                'name',
                $data['name']
            );

            if (
                !empty($data['model'])
            ) {

                $query->where(
                    'model',
                    $data['model']
                );
            }

            $product =
            $query->first();

            if ($product) {
                return $product;
            }
        }

        return new Product([
            'id' =>
            $this->generateProductCode(),
        ]);
    }

    protected function generateProductCode(): string
    {
        do {

            $code =
            'P-' .
                strtoupper(
                    Str::random(8)
                );

        } while (
            Product::where(
                'id',
                $code
            )->exists()
        );

        return $code;
    }

    /*
    |--------------------------------------------------------------------------
    | Product data
    |--------------------------------------------------------------------------
    */

    protected function fillProduct(
        Product $product,
        array $data
    ): void {

        if (
            $data['name'] !== ''
        ) {
            $product->name =
            $data['name'];
        }

        if (
            $data['model'] !== ''
        ) {
            $product->model =
            $data['model'];
        }

        if (
            $data['description'] !== ''
        ) {
            $product->description =
            $data['description'];
        }

        if (
            $data['price'] !== ''
        ) {
            $product->price =
            $data['price'];
        }

        if (
            $data['color'] !== ''
        ) {
            $product->color =
            $data['color'];
        }

        if (
            $data['parent_id'] !== ''
        ) {
            $product->parent_id =
            $data['parent_id'];
        }

        if (
            $data['wholesale_price'] !== ''
        ) {
            $product->wholesale_price =
            $data['wholesale_price'];
        }

        if (
            $data['category'] !== ''
        ) {

            $category =
            Category::firstOrCreate([
                'name' =>
                trim(
                    $data['category']
                ),
            ]);

            $product->category_id =
            $category->id;
        }

        if (
            $data['brand'] !== ''
        ) {

            $brand =
            $this->findOrCreateBrand(
                $data['brand']
            );

            $product->brand_id =
            $brand->id;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Brand
    |--------------------------------------------------------------------------
    */

    protected function findOrCreateBrand(
        string $name,
        ?string $made = 'none',
        ?string $bgColor = 'black',
        ?string $fbColor = 'white'
    ): Brand {

        $name = trim($name);

        $brand =
        Brand::where(
            'name',
            $name
        )->first();

        if ($brand) {

            $updates = [];

            if (
                $made !== null &&
                    $made !== ''
            ) {
                $updates['made'] =
                $made;
            }

            if (
                $bgColor !== null &&
                    $bgColor !== ''
            ) {
                $updates['bg_color'] =
                $bgColor;
            }

            if (
                $fbColor !== null &&
                    $fbColor !== ''
            ) {
                $updates['color_fg'] =
                $fbColor;
            }

            if (!empty($updates)) {
                $brand->update(
                    $updates
                );
            }

            return $brand;
        }

        return Brand::create([
            'name' =>
            $name,

            'made' =>
            $made !== null &&
                $made !== ''
            ? $made
            : 'GENERICO',

            'color_bg' =>
            $bgColor !== null &&
                $bgColor !== ''
            ? $bgColor
            : 'white',

            'color_fg' =>
            $fbColor !== null &&
                $fbColor !== ''
            ? $fbColor
            : 'black',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Tags
    |--------------------------------------------------------------------------
    */

    protected function importProductTags(
        Product $product,
        array $tags
    ): void {
        foreach ($tags as $tag) {

            $name = trim($tag['name'] ?? '');
            $value = trim($tag['value'] ?? '');

            if ($name === '' || $value === '') {
                continue;
            }

            DB::table('tag_products')->insert([
                'product_id' => $product->id,
                'name' => $name,
                'value' => $value,
            ]);
        }
    }

    public function addTagMapping(): void
    {
        $this->mapping['product']['tags'][] =
        '';
    }

    public function removeTagMapping(
        int $index
    ): void {

        unset(
        $this->mapping['product']['tags'][$index]
    );

        $this->mapping['product']['tags'] =
        array_values(
            $this->mapping['product']['tags']
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Restart
    |--------------------------------------------------------------------------
    */

    protected function resetImportData(): void
    {
        $this->sheetNames = [];

        $this->selectedSheet = 0;

        $this->headers = [];

        $this->preview = [];

        $this->rows = [];

        $this->totalRows = 0;

        $this->step = 1;

        $this->mapping = [
            'product' => [
                'id' => '',
                'name' => '',
                'model' => '',
                'description' => '',
                'price' => '',
                'color' => '',
                'parent_id' => '',
                'wholesale_price' => '',
                'category' => '',
                'brand' => '',
                'tags' => [],
            ],

            'category' => [
                'name' => '',
            ],

            'brand' => [
                'name' => '',
                'made_type' => 'fixed',
                'made' => '',
                'bg_color_type' => 'fixed',
                'bg_color' => '',
                'fb_color_type' => 'fixed',
                'fb_color' => '',
            ],
        ];

        $this->mappedData = [
            'products' => [],
            'categories' => [],
            'brands' => [],
        ];
    }

    public function restart()
    {
        $this->reset([
            'file',
        ]);

        $this->resetValidation();

        $this->resetImportData();
    }

    public function render()
    {
        return view(
            'livewire.import-product'
        );
    }
}
