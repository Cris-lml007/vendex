<?php

namespace App\Livewire;

use App\Models\Customer;
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

    public function updating()
    {
        $this->resetPage();

    }

    public function search()
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
                $query->whereDate('created_at', '>=', $this->from);
            })

            ->when($this->to, function (Builder $query) {
                $query->whereDate('created_at', '<=', $this->to);
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
        return $this->query()
            ->selectRaw('DATE(created_at) as date')
            ->selectRaw('COUNT(*) as sales')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();
    }
    
    public function getStoresChart()
    {
        return $this->query()
            ->join('stores', 'transactions.store_id', '=', 'stores.id')
            ->selectRaw('stores.name as store')
            ->selectRaw('COUNT(*) as sales')
            ->groupBy('stores.id', 'stores.name')
            ->orderBy('stores.name')
            ->get();
    }

    public function render()
    {
        $this->calculateResume();
        $salesChart = $this->getSalesChart();
        $storesChart = $this->getStoresChart();
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
        ]);
    }
}
