<x-slot name="header">
    <div class="container-fluid">
        <h1>Reportes</h1>
    </div>
</x-slot>

<div>
    <style>
        .apexcharts-legend-text {
            color: white !important;
        }
    </style>

    <div class="container-fluid">

        {{-- FILTROS --}}
        <div class="card shadow-sm mb-3">
            <div class="card-header">
                <h5 class="mb-0">
                    Reporte de Ventas
                </h5>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-2 mb-3">
                        <label>Tienda</label>
                        <select class="form-select" wire:model.live="store">
                            <option value="">Todas</option>

                            @foreach($stores ?? [] as $store)
                                <option value="{{ $store->id }}">
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Cliente</label>

                        <select class="form-select" wire:model.live="customer">
                            <option value="">Todos</option>

                            @foreach($customers ?? [] as $customer)
                                <option value="{{ $customer->id }}">
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Vendedor</label>

                        <select class="form-select" wire:model.live="user">
                            <option value="">Todos</option>

                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Desde</label>

                        <input
                            type="date"
                            class="form-control"
                            wire:model="from">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label>Hasta</label>

                        <input
                            type="date"
                            class="form-control"
                            wire:model="to">
                    </div>

                    <div class="col-md-2 mb-3 d-flex align-items-end">

                        <button
                            class="btn btn-primary w-100"
                            wire:click="search">

                            Buscar

                        </button>

                    </div>

                </div>

            </div>

        </div>

        {{-- RESUMEN --}}
        <div class="row mb-3">

            <div class="col-md-3">

                <div class="card border-success">

                    <div class="card-body">

                        <small class="text-white">Total Ventas</small>

                        <h3 class="mb-0 text-white">
                            {{ $totalSales ?? 0 }}
                        </h3>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card border-primary">

                    <div class="card-body">

                        <small class="text-white">Ingreso Total</small>

                        <h3 class="mb-0 text-white">

                            Usd.
                            {{ number_format($totalAmount,2) }}

                        </h3>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card border-warning">

                    <div class="card-body">

                        <small class="text-white">Productos Vendidos</small>

                        <h3 class="mb-0 text-white">

                            {{ $productsSold ?? 0 }}

                        </h3>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card border-info">

                    <div class="card-body">

                        <small class="text-white">Ticket Promedio</small>

                        <h3 class="mb-0 text-white">

                            Usd.
                            {{ number_format($averageSale ?? 0,2) }}

                        </h3>

                    </div>

                </div>

            </div>

        </div>

        <div class="row mb-3">

            <div class="col-lg-8">

                <div class="card">

                    <div class="card-header">

                        Ventas por Día

                    </div>

                    <div class="card-body">

                        <div wire:ignore id="sales-chart"></div>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="card">

                    <div class="card-header">

                        Ventas por Tienda

                    </div>

                    <div class="card-body">

                        <div wire:ignore id="stores-chart"></div>

                    </div>

                </div>

            </div>

        </div>

        <div class="card">

            <div class="card-header">
                <h5 class="mb-0">
                    Productos más vendidos
                </h5>
            </div>

            <table class="table table-striped table-hover">

                <thead>

                <tr>

                    <th>#</th>

                    <th>Producto</th>

                    <th class="text-center">
                        Cantidad
                    </th>

                    <th class="text-end">
                        Total
                    </th>

                </tr>

                </thead>

                <tbody>

                @foreach($bestSellingProducts as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item->name }}
                        </td>

                        <td class="text-center">
                            {{ number_format($item->quantity) }}
                        </td>

                        <td class="text-end">
                            $ {{ number_format($item->total,2) }}
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>



        <div class="card">

            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h5 class="mb-0">Existencias por Tienda</h5>
                    <input type="text" class="form-control w-25" placeholder="Buscar..." wire:model.blur.enter.live="search">
                </div>
            </div>

            <div class="table-responsive">

                <table class="table table-bordered table-hover table-striped mb-0">

                    <thead>

                    <tr>

                        <th>Producto</th>

                        @foreach($stores as $store)

                            <th class="text-center">

                                {{ $store->name }}

                            </th>

                        @endforeach

                        <th class="text-center bg-primary">

                            Total

                        </th>

                    </tr>

                    </thead>

                    <tbody>

                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            @foreach($stores as $s)
                                <td>{{ $product->stocks()?->where('store_id', $s->id)?->first()?->quantity ?? 0 }}</td>
                            @endforeach
                            <td>{{ $product->stocks()->sum('quantity') ?? 0 }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <td><strong>TOTALES</strong></td>
                        @foreach($stores as $s)
                            <td>{{ $s->stocks()->whereHas('product',function ($builder){

            $terms = preg_split('/\s+/', trim($this->search ?? ''));

            $builder->where('status', \App\Enums\Status::ACTIVE)
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
})->sum('quantity') ?? 0 }}</td>
                        @endforeach
                        <td>{{ \App\Models\Stock::whereHas('product',function ($builder){

            $terms = preg_split('/\s+/', trim($this->search ?? ''));

            $builder->where('status', \App\Enums\Status::ACTIVE)
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
})->sum('quantity') ?? 0 }}</td>
                    </tfoot>
                </table>

            </div>

        </div>



        {{-- TABLA --}}
        <div class="card shadow-sm">

            <div class="card-header d-flex justify-content-between">

                <h5 class="mb-0">

                    Ventas

                </h5>

                <div>

                    <button wire:click="exportPdf" class="btn btn-danger btn-sm"><i class="fa fa-file-pdf"></i> Exportar PDF</button>

                </div>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">

                        <thead>

                        <tr>

                            <th>#</th>

                            <th>Fecha</th>

                            <th>Tienda</th>

                            <th>Cliente</th>

                            <th>Vendedor</th>

                            <th class="text-center">

                                Productos

                            </th>

                            <th class="text-end">

                                Total

                            </th>

                        </tr>

                        </thead>

                        <tbody>

                        @forelse($transactions ?? [] as $transaction)

                            <tr>

                                <td>

                                    {{ $transaction->id }}

                                </td>

                                <td>

                                    {{ $transaction->created_at->format('d/m/Y H:i') }}

                                </td>

                                <td>

                                    {{ $transaction->store->name }}

                                </td>

                                <td>

                                    {{ $transaction?->customer?->name ?? '---'}}

                                </td>

                                <td>

                                    {{ $transaction->user->name }}

                                </td>

                                <td class="text-center">

                                    {{ $transaction->details()->sum('quantity') }}

                                </td>

                                <td class="text-end">

                                    Usd.
                                    {{ number_format($transaction->total,2) }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center">
                                    No existen registros.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>

@script
    <script>

        document.addEventListener('livewire:initialized',()=>{

            const options = {

                chart:{
                    type:'line',
                    height:350
                },
                series: [{
                    name: 'Ventas',
                    data: @json($series)
                }],
                xaxis: {
                    categories: @json($labels)
                }
            };

            const salesChart = new ApexCharts(
                document.querySelector("#sales-chart"),
                options
            );

            salesChart.render();

            const storeOptions = {

                chart: {
                    type: 'bar',
                    height: 350
                },

                series: [{
                    name: 'Ventas',
                    data: @json($storeSeries)
                }],

                xaxis: {
                    categories: @json($storeLabels)
                }

            };

            const storeChart = new ApexCharts(
                document.querySelector("#stores-chart"),
                storeOptions
            );

            storeChart.render();
        });

    </script>
@endscript
