<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Ventas Realizadas</h1>
    </div>
</x-slot>

<div>
    <div class="d-flex justify-content-end mb-2">
        <div class="input-group w-50">
            <button wire:click="$set('today',false)" @class([
                'btn',
                'input-group-text',
                'btn-primary' => !$today,
                'btn-secondary' => $today,
            ])>Todas las Ventas</button>
            <button wire:click="$set('today',true)" @class([
                'btn',
                'input-group-text',
                'btn-primary' => $today,
                'btn-secondary' => !$today,
            ])>Ventas de Hoy</button>
            <select class="form-select w-25" wire:model.live="store" @if ($lock) disabled @endif>
                <option value="">Seleccione Tienda</option>
                @foreach ($stores as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <x-card>
        <livewire:table :heads="$heads" wire:model.live="list">
            @foreach ($data ?? [] as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->customer->name ?? '---' }}</td>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ \Illuminate\Support\Number::format($item->totalBs ?? 0, 2) }}
                    </td>
                    <td>{{ $item->created_at }}</td>
                    <td>
                        <button data-bs-toggle="modal" data-bs-target="#modal-sale"
                            wire:click="getTransaction({{ $item->id }})" class="btn btn-primary"><i
                                class="fa fa-eye"></i></button>
                    </td>
                </tr>
            @endforeach

            <livewire:slot name="footer">
                <th colspan="3">TOTAL</th>
                <th colspan="3">{{ Number::format($total ?? 0,2) }} Bs</th>
            </livewire:slot>
        </livewire:table>
        {{-- {{ $data->links() }} --}}
    </x-card>

    @island
        <x-modal id="modal-sale" title="Registro de Venta" class="modal-lg">
            <livewire:sale-form></livewire:sale-form>
        </x-modal>
    @endisland
</div>
