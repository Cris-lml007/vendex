<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Clientes</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-customers" class="btn btn-primary"><i class="fa fa-plus"></i> Añadir Nuevo Cliente</button>
    </div>
</x-slot>

<div>
    <x-card>
        <livewire:table :heads="$heads" wire:model.live="list">
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->ci }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->phone }}</td>
                    <td>{{ $item->email }}</td>
                    <td>
                        <button data-bs-target="#modal-customers" data-bs-toggle="modal" wire:click="getCustomer({{ $item->id }})" class="btn btn-primary"><i class="fa fa-eye"></i></button>
                    </td>
                </tr>
            @endforeach
        </livewire:table>
    </x-card>

    @island
    <x-modal id="modal-customers" title="Nuevo Cliente">
        <livewire:customers-form></livewire:customers-form>
    </x-modal>
    @endisland
</div>
