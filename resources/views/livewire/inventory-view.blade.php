<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Inventario</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-inventory" class="btn btn-primary"><i class="fa fa-plus"></i> Añadir Nuevo Lote</button>
    </div>
</x-slot>

<div>
    <div>
        <x-card>
            <livewire:table :heads="$heads">
                @foreach($data as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->type->name }}</td>
                        <td>{{ $item->store->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>
                            <button class="btn btn-primary"><i class="fa fa-pen"></i></button>
                            <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
            </livewire:table>
        </x-card>
    </div>

    <x-modal id="modal-inventory" title="Inventario" class="modal-lg">
        <livewire:inventory-form></livewire:inventory-form>
    </x-modal>
</div>
