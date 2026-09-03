<?php

namespace App\Livewire;

use App\Enums\Status;
use App\Models\Customer;
use App\Models\DetailTransaction;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Livewire\WithPagination;

class ReportView extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filtros
    public $store = '';
    public $customer = '';
    public $user = '';

    public $from = '';
    public $to = '';

    // KPIs
    public $totalSales = 0;
    public $totalAmount = 0;
    public $productsSold = 0;
    public $averageSale = 0;

    public $search;

    public function updating()
    {
        $this->resetPage();

    }

    public function searchFilter()
    {
        // Solo reinicia la paginación.
        // Los datos se recalculan automáticamente en render().
        $this->resetPage();
    }

    protected function query()
    {
        return Transaction::query()
            ->with([
                'store',
                'customer',
                'user',
                'details'
            ])
            ->withCount('details')

            ->when($this->store, function (Builder $query) {
                $query->where('store_id', $this->store);
            })

            ->when($this->customer, function (Builder $query) {
                $query->where('customer_id', $this->customer);
            })

            ->when($this->user, function (Builder $query) {
                $query->where('user_id', $this->user);
            })

            ->when($this->from, function (Builder $query) {
                dd("hol");
                $query->whereDate('transactions.created_at', '>=', $this->from);
            })

            ->when($this->to, function (Builder $query) {
                $query->whereDate('transactions.created_at', '<=', $this->to);
            });
    }

    protected function calculateResume()
    {
        $transactions = $this->query()->get();

        $this->totalSales = $transactions->count();

        $this->productsSold = $transactions->sum(function ($transaction) {
            return $transaction->details->sum('quantity');
        });

        $this->totalAmount = $transactions->sum(function ($transaction) {
            return $transaction->details->sum(function ($detail) {
                return $detail->quantity * $detail->price;
            });
        });

        $this->averageSale = $this->totalSales > 0
            ? $this->totalAmount / $this->totalSales
            : 0;
    }

    public function exportPdf()
    {
        $transactions = $this->query()
            ->with([
                'store',
                'customer',
                'user',
                'details.product'
            ])
            ->get();

        $totalSales = $transactions->count();

        $productsSold = $transactions->sum(function ($transaction) {
            return $transaction->details->sum('quantity');
        });

        $totalAmount = $transactions->sum(function ($transaction) {
            return $transaction->details->sum(function ($detail) {
                return $detail->quantity * $detail->price;
            });
        });

        $averageSale = $totalSales > 0
            ? $totalAmount / $totalSales
            : 0;

        $pdf = Pdf::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
        ])->loadView('pdf.report', [
            'transactions' => $transactions,
            'totalSales' => $totalSales,
            'productsSold' => $productsSold,
            'totalAmount' => $totalAmount,
            'averageSale' => $averageSale,
            'from' => $this->from,
            'to' => $this->to,
            'store' => $this->store,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'Reporte_Ventas_'.now()->format('Ymd_His').'.pdf'
        );
    }

    public function getSalesChart()
    {
        $query = $this->query();

        $query->getQuery()->columns = null;

        return $query
            ->selectRaw('DATE(transactions.created_at) as date, COUNT(*) as sales')
            ->groupByRaw('DATE(transactions.created_at)')
            ->orderByRaw('DATE(transactions.created_at)')
            ->get();
    }

    public function getStoresChart()
    {
        $query = $this->query();

        $query->getQuery()->columns = null;
        return $query
            ->join('stores', 'transactions.store_id', '=', 'stores.id')
            ->selectRaw('stores.name as store, COUNT(*) as sales')
            ->groupBy('stores.id', 'stores.name')
            ->orderBy('stores.name')
            ->get();
    }

    public function getProductsChart()
    {
        return Transaction::query()
            ->join('detail_transactions', 'transactions.id', '=', 'detail_transactions.transaction_id')
            ->join('products', 'detail_transactions.product_id', '=', 'products.id')

            ->when($this->store, fn($q) => $q->where('transactions.store_id', $this->store))
            ->when($this->customer, fn($q) => $q->where('transactions.customer_id', $this->customer))
            ->when($this->user, fn($q) => $q->where('transactions.user_id', $this->user))
            ->when($this->from, fn($q) => $q->whereDate('transactions.created_at', '>=', $this->from))
            ->when($this->to, fn($q) => $q->whereDate('transactions.created_at', '<=', $this->to))

            ->selectRaw('products.name as product')
            ->selectRaw('SUM(detail_transactions.quantity) as sold')

            ->groupBy('products.id', 'products.name')

            ->orderByDesc('sold')

            ->limit(10)

            ->get();
    }

    public function getStockChart()
    {
        return Product::query()

            ->whereNull('products.parent_id')

            ->select(
                'products.id',
                'products.name'
            )

            ->selectRaw('
            (
                SELECT COALESCE(SUM(quantity),0)
                FROM stocks
                WHERE stocks.product_id = products.id
                ' . ($this->store ? 'AND stocks.store_id = '.$this->store : '') . '
            )
            +
            (
                SELECT COUNT(*)
                FROM products serials
                WHERE serials.parent_id = products.id
                ' . ($this->store ? 'AND serials.store_id = '.$this->store : '') . '
                and serials.status != 4
            )
            AS stock
        ')

            ->orderByDesc('stock')

            ->limit(10)

            ->get();
    }

    public function getStockTable()
    {
        $stores = Store::orderBy('name')->get();

        // Stock de productos por lote
        $stocks = Stock::selectRaw('
            product_id,
            store_id,
            SUM(quantity) as quantity
        ')
            ->groupBy('product_id', 'store_id')
            ->get();

        // Cantidad de productos serializados
        $serials = Product::selectRaw('
            parent_id as product_id,
            store_id,
            COUNT(*) as quantity
        ')
            ->whereNotNull('parent_id')
            ->groupBy('parent_id', 'store_id')
            ->get();

        // Productos padre
        $products = Product::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return $products->map(function ($product) use ($stores, $stocks, $serials) {

            $row = [
                'product' => $product->name,
            ];

            $total = 0;

            foreach ($stores as $store) {

                $stock = optional(
                    $stocks
                        ->where('product_id', $product->id)
                        ->where('store_id', $store->id)
                        ->first()
                )->quantity ?? 0;

                $serialized = optional(
                    $serials
                        ->where('product_id', $product->id)
                        ->where('store_id', $store->id)
                        ->first()
                )->quantity ?? 0;

                $quantity = $stock + $serialized;

                $row[$store->id] = $quantity;

                $total += $quantity;
            }

            $row['total'] = $total;

            return $row;
        });
    }

    public function getBestSellingProducts()
    {
        return DetailTransaction::query()

            ->join('transactions', 'detail_transactions.transaction_id', '=', 'transactions.id')

            ->join('products', 'detail_transactions.product_id', '=', 'products.id')

            ->when($this->store, function ($query) {
                $query->where('transactions.store_id', $this->store);
            })

            ->when($this->customer, function ($query) {
                $query->where('transactions.customer_id', $this->customer);
            })

            ->when($this->user, function ($query) {
                $query->where('transactions.user_id', $this->user);
            })

            ->when($this->from, function ($query) {
                $query->whereDate('transactions.created_at', '>=', $this->from);
            })

            ->when($this->to, function ($query) {
                $query->whereDate('transactions.created_at', '<=', $this->to);
            })

            ->select(
                'products.id',
                'products.name'
            )

            ->selectRaw('SUM(detail_transactions.quantity) as quantity')

            ->selectRaw('SUM(detail_transactions.quantity * detail_transactions.price) as total')

            ->groupBy(
                'products.id',
                'products.name'
            )

            ->orderByDesc('quantity')

            ->limit(10)

            ->get();
    }


    public function render()
    {
        $this->calculateResume();
        $salesChart = $this->getSalesChart();
        $storesChart = $this->getStoresChart();
        $productsChart = $this->getProductsChart();

        $stockChart = $this->getStockChart();
        if($this->search != ''){

            $terms = preg_split('/\s+/', trim($this->search));

            $products = Product::where('status', Status::ACTIVE)
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
                ->get();
        }else{
            $products = Product::all();
        }

        return view('livewire.report-view', [

            'transactions' => $this->query()
                ->latest()
                ->paginate(15),

            'stores' => Store::orderBy('name')->get(),

            'customers' => Customer::orderBy('name')->get(),

            'users' => User::orderBy('name')->get(),
            'salesChart' => $salesChart->toArray(),
            'labels' => $salesChart->pluck('date'),
            'series' => $salesChart->pluck('sales'),
            'storeLabels' => $storesChart->pluck('store'),
            'storeSeries' => $storesChart->pluck('sales'),
            'productLabels' => $productsChart->pluck('product'),
            'productSeries' => $productsChart->pluck('sold'),

            'stockLabels' => $stockChart->pluck('name'),
            'stockSeries' => $stockChart->pluck('stock'),
            'stockTable' => $this->getStockTable(),
            'bestSellingProducts' => $this->getBestSellingProducts(),
            'products' => $products
        ]);
    }
}
