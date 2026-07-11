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
                        <td><strong style="color: {{ $item->brand->color_fg }}; background: {{ $item->brand->color_bg }}">{{ $item->brand->name }}</strong></td>
                        <td>{{ $item?->category?->name ?? '---' }}</td>
                        <td>{{ Number::format($item->price, precision: 2) }}</td>
                    <td>
                        <a href="{{ route('admin.product.id',$item->id) }}" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                    </td>
                    </tr>
                @endforeach
            </livewire:table>
        </x-card>
    </div>

    @island
    <x-modal id="modal-product" title="Nuevo Producto">
        <livewire:product-form></livewire:product-form>
    </x-modal>
    @endisland

    @island

    <x-modal id="modal-scanner" title="Escaner">
        <livewire:scanner wire:model.live="product_id"></livewire:scanner>
    </x-modal>
    @endisland
</div>
