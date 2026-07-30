<x-slot name="header">
    <h1>Tasa de Conversion</h1>
</x-slot>

<div>
    <x-card>
        <div class="row mb-1">
            <div class="col">
                <label for="">Configurar Tasa de Cambio</label>
                <div class="input-group">
                    <div class="input-group-text">Usd</div>
                    <input type="text" class="form-control" value="1.00" disabled>
                    <div class="input-group-text"><i class="fa fa-share"></i></div>
                    <input type="text" class="form-control" wire:model="bs">
                    <div class="input-group-text">Bs</div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <button wire:click="save" class="btn btn-primary w-100">Guardar</button>
            </div>
        </div>
    </x-card>

    <x-card>
        <h5 class="text-white">Historial de Cambios</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <th>Fecha</th>
                <th>Cambio (Bs)</th>
                </thead>
                <tbody>
                @foreach($exchange_rates ?? [] as $item)
                    <tr>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->usd_to_bs }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
