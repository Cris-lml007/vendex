<x-slot name="header">
    <h1>Asistencias</h1>
</x-slot>

<div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-filter me-2"></i>
            Filtros
        </div>

        <div class="card-body">
            <div class="row g-3">

                {{-- Usuario --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Usuario</label>
                    <select class="form-select" wire:model.live="user">
                        <option value="">Todos</option>
                        @foreach($users as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tienda --}}
                <div class="col-lg-3 col-md-6">
                    <label class="form-label">Tienda</label>
                    <select class="form-select" wire:model.live="store">
                        <option value="">Todas</option>
                        @foreach($stores as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Desde --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Desde</label>
                    <input
                        type="date"
                        class="form-control"
                        wire:model.live="from">
                </div>

                {{-- Hasta --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Hasta</label>
                    <input
                        type="date"
                        class="form-control"
                        wire:model.live="to">
                </div>

                {{-- Estado --}}
                <div class="col-lg-2 col-md-6">
                    <label class="form-label">Estado</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="">Todos</option>
                        <option value="present">Asistencia</option>
                        <option value="late">Atrasados</option>
                        <option value="nosale">Sin salida</option>
                        <option value="absent">Faltas</option>
                    </select>
                </div>

            </div>

            <hr>

            <div class="d-flex justify-content-end">

                <button
                    class="btn btn-primary me-2"
                    wire:click="search">

                    <i class="fas fa-search me-1"></i>

                    Buscar

                </button>


            </div>

        </div>
    </div>
    <div class="row">

        {{-- Personal --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Personal</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        {{-- Asistencias --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalAttendance }}</h3>
                    <p>Asistencias</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>

        {{-- Puntuales --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $onTime }}</h3>
                    <p>Puntuales</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        {{-- Atrasos --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $late }}</h3>
                    <p>Atrasados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        {{-- Sin salida --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $withoutExit }}</h3>
                    <p>Sin salida</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
            </div>
        </div>

        {{-- Faltas --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $absences }}</h3>
                    <p>Faltas</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
        </div>

        {{-- Promedio atraso --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-dark">
                <div class="inner">
                    <h3>{{ $averageLate }} min</h3>
                    <p>Promedio de atraso</p>
                </div>
                <div class="icon">
                    <i class="fas fa-stopwatch"></i>
                </div>
            </div>
        </div>

        {{-- Horas promedio --}}
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="small-box bg-indigo">
                <div class="inner">
                    <h3>{{ number_format($averageWorked,1) }} h</h3>
                    <p>Horas promedio</p>
                </div>
                <div class="icon">
                    <i class="fas fa-business-time"></i>
                </div>
            </div>
        </div>

        <div class="card">

            <div class="card-header">

                Asistencias por Usuario

            </div>

            <div class="card-body">

                <div id="attendance-chart"></div>

            </div>

        </div>

        <div class="card mb-3">

            <div class="card-header">
                <i class="fas fa-list-check me-2"></i>
                Resumen de Asistencias
            </div>

            <div class="card-body table-responsive p-0">

                <table class="table table-hover align-middle mb-0">

                    <thead>
                    <tr>

                        <th>Usuario</th>

                        <th>Tienda</th>

                        <th>Asistencias</th>

                        <th>Puntuales</th>

                        <th>Atrasos</th>

                        <th>Sin Salida</th>

                        <th>Prom. Entrada</th>

                        <th>Prom. Salida</th>

                        <th>Estado</th>

                    </tr>
                    </thead>

                    <tbody>

                    @forelse($attendances as $item)

                        <tr>

                            <td>{{ $item->user }}</td>

                            <td>{{ $item->store }}</td>

                            <td>
                        <span class="badge bg-primary">
                            {{ $item->attendance }}
                        </span>
                            </td>

                            <td>
                        <span class="badge bg-success">
                            {{ $item->ontime }}
                        </span>
                            </td>

                            <td>
                        <span class="badge bg-warning">
                            {{ $item->late }}
                        </span>
                            </td>

                            <td>
                        <span class="badge bg-danger">
                            {{ $item->without_exit }}
                        </span>
                            </td>

                            <td>

                                {{ $item->avg_entry }}

                            </td>

                            <td>

                                {{ $item->avg_exit }}

                            </td>

                            <td>

                                @php

                                    $percentage = $item->attendance > 0
                                        ? ($item->ontime * 100 / $item->attendance)
                                        : 0;

                                @endphp

                                @if($percentage >= 95)

                                    <span class="badge bg-success">
                                Excelente
                            </span>

                                @elseif($percentage >= 80)

                                    <span class="badge bg-warning">
                                Regular
                            </span>

                                @else

                                    <span class="badge bg-danger">
                                Crítico
                            </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9" class="text-center py-4">

                                No existen registros.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

            <div class="card-footer">

                {{ $attendances->links() }}

            </div>

        </div>
    </div>

</div>

@script
    <script>
        const attendanceOptions = {

            chart: {
                type: 'bar',
                height: 450
            },

            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4
                }
            },

            series: [{
                name: 'Asistencias',
                data: @json($attendanceSeries)
            }],

            xaxis: {
                categories: @json($attendanceLabels)
            },

            dataLabels: {
                enabled: true
            }

        };

        const attendanceChart = new ApexCharts(
            document.querySelector("#attendance-chart"),
            attendanceOptions
        );

        attendanceChart.render();
    </script>
@endscript
