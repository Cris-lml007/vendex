<div>
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

                        <small>Total Ventas</small>

                        <h3 class="mb-0">
                            {{ $totalSales ?? 0 }}
                        </h3>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card border-primary">

                    <div class="card-body">

                        <small>Ingreso Total</small>

                        <h3 class="mb-0">

                            Bs.
                            {{ number_format($totalAmount,2) }}

                        </h3>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card border-warning">

                    <div class="card-body">

                        <small>Productos Vendidos</small>

                        <h3 class="mb-0">

                            {{ $productsSold ?? 0 }}

                        </h3>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card border-info">

                    <div class="card-body">

                        <small>Ticket Promedio</small>

                        <h3 class="mb-0">

                            Bs.
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

                        <div id="sales-chart"></div>

                    </div>

                </div>

            </div>

            <div class="col-lg-4">

                <div class="card">

                    <div class="card-header">

                        Ventas por Tienda

                    </div>

                    <div class="card-body">

                        <div id="stores-chart"></div>

                    </div>

                </div>

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

                                Bs.
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

            window.salesChart = new ApexCharts(
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


        Livewire.on('updateSalesChart',(event)=>{

            salesChart.updateOptions({

                xaxis:{
                    categories:event.labels
                }

            });

            salesChart.updateSeries([{

                data:event.series

            }]);

        });

    </script>
@endscript
