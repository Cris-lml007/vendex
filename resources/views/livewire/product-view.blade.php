<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Catalogo de Productos</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-product" class="btn btn-primary"><i class="fa fa-plus"></i>
            Añadir Nuevo Producto</button>
    </div>
</x-slot>

<div>
    <div>
        <x-card>
            <livewire:table :heads="$heads">
                @foreach ($products as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->model }}</td>
                        <td>{{ $item->brand }}</td>
                        <td>{{ $item?->category?->name ?? '---' }}</td>
                        <td>{{ Number::format($item->price, precision: 2) }}</td>
                    <td>
                        <button class="btn btn-primary"><i class="fa fa-eye"></i></button>
                    </td>
                    </tr>
                @endforeach
            </livewire:table>
        </x-card>
    </div>

    <x-modal id="modal-product" title="Nuevo Producto">
        <livewire:product-form></livewire:product-form>
    </x-modal>
</div>
