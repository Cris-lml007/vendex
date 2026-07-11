<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Ventas Realizadas</h1>
    </div>
</x-slot>

<div>
    <div class="d-flex justify-content-end mb-2">
        <select class="form-select w-25" wire:model.live="store">
            <option value="">Seleccione Tienda</option>
            @foreach($stores as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
    <x-card>
        <livewire:table :heads="$heads">
            @foreach($data ?? [] as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->customer->name ?? '---' }}</td>
                    <td>{{ $item->user->name }}</td>
                    <td>{{ \Illuminate\Support\Number::format($item->total,2) }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td>
                        <button data-bs-toggle="modal" data-bs-target="#modal-sale" wire:click="getTransaction({{ $item->id }})" class="btn btn-primary"><i class="fa fa-eye"></i></button>
                    </td>
                </tr>
            @endforeach
        </livewire:table>
    </x-card>

    @island
    <x-modal id="modal-sale" title="Registro de Venta" class="modal-lg">
        <livewire:sale-form></livewire:sale-form>
    </x-modal>
    @endisland
</div>
