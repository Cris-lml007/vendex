<x-slot name="header">
    <h1>Perfil</h1>
</x-slot>

<div>
    <x-card>
        <div class="row mb-3">
            <div class="col">
                <label for="">Nombre Completo</label>
                <input type="text" class="form-control" placeholder="Ingrese Nombre Completo" value="{{ $user->name }}" disabled>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col">
                <label for="">Celular</label>
                <input type="text" class="form-control" placeholder="Ingrese numero de Celular" value="{{ $user->phone }}" disabled>
                @error('phone')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Usuario</label>
                <input type="text" class="form-control" placeholder="Ingrese usuario" value="{{ $user->username }}" disabled>
                @error('username')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col">
                <label for="">Rol</label>
                <select name="" id="" class="form-select" disabled>
                        <option>{{ __('messages.'.$user->role->name) }}</option>
                </select>
                @error('role')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Tienda</label>
                <select class="form-select" disabled>
                    <option value="">{{ $user?->store?->name ?? '' }}</option>
                </select>
                @error('store_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col">
                <label for="">Estado</label>
                <select name="" id="" class="form-select" disabled>
                    <option value="">{{ __('messages.'.$user->status->name) }}</option>
                </select>
                @error('status')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Hora de Ingreso</label>
                <input type="time" class="form-control" value="{{ $user->entry_time }}" disabled>
            </div>
            <div class="col">
                <label for="">Hora de Ingreso</label>
                <input type="time" class="form-control" value="{{ $user->exit_time }}" disabled>
            </div>
        </div>
        <h5 class="text-white">Asistencia</h5>
        <label for="">marcar Asistencia</label>
        <div class="d-flex justify-content-center mb-3">
            <button wire:click="entry" class="btn btn-success w-50">Ingreso</button>
            <button wire:click="sale" class="btn btn-danger w-50">Salida</button>
        </div>
        <div class="table-fluid">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th colspan="3" class="text-center"><strong>ASISTENCIAS</strong></th>
                </tr>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                @foreach($user->attendances as $item)
                    <tr>
                        <td>{{ $item->created_at }}</td>
                        <td><p @class(['text-center' ,'bg-success' => $item->type == \App\Enums\Type::IN, 'bg-danger' => $item->type == \App\Enums\Type::OUT])>{{ __('messages.'.$item->type->name) }}</p></td>
                        @if( $item->type == \App\Enums\Type::IN)
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Hi') >= \Carbon\Carbon::parse($user->entry_time)->addMinutes(5)->format('Hi') ? 'Atraso' : 'A Tiempo' }}</td>
                        @else
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Hi') <= \Carbon\Carbon::parse($user->entry_time)->addMinutes(5)->format('Hi') ? 'Atraso' : 'A Tiempo' }}</td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>

@script
    <script>
        if (!navigator.geolocation) {
            Swal.fire({
                icon: 'error',
                title: 'Su navegador no soporta geolocalización'
            });
            return;
        }

        navigator.geolocation.getCurrentPosition(

            function(position) {

                @this.sale(
                    position.coords.latitude,
                    position.coords.longitude
                );

            },

            function(error) {

                Swal.fire({
                    icon: 'error',
                    title: 'No fue posible obtener su ubicación'
                });
                console.log(error)
            },

            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }

        );
    </script>
@endscript
