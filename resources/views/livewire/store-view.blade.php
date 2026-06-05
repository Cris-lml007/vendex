<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Tiendas y Almacenes</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-store" class="btn btn-primary"><i class="fa fa-plus"></i> Añadir Nueva Locación</button>
    </div>
</x-slot>

<div>
    <div>
        <x-card>
            <livewire:table :heads="$heads">
                @foreach ($stores as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->type->name }}</td>
                    <td>{{ $item->status->name }}</td>
                    <td></td>
                </tr>
                @endforeach
            </livewire:table>
        </x-card>
    </div>

    <x-modal id="modal-store" title="Tiendas y Almacenes" class="modal-lg">
        <livewire:store-form></livewire:store-form>
    </x-modal>
</div>
