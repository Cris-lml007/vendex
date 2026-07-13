<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Catalogo</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-scanner" class="btn btn-primary"><i class="fa fa-qrcode"></i> Buscar por Codigo de Barras</button>
    </div>
</x-slot>

<div>
    <x-card>
        <livewire:table :heads='$heads' wire:model.live="list">
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->model }}</td>
                    <td>{{ $item->brand->name }}</td>
                    <td>{{ Number::format($item->price,2) }}</td>
                    <td>
                        <button wire:click="getProduct('{{ $item->id }}')" data-bs-toggle="modal" data-bs-target="#modal-product" class="btn btn-primary"><i class="fa fa-eye"></i></button>
                    </td>
                </tr>
            @endforeach
        </livewire:table>
    </x-card>



    @island
    <x-modal id="modal-scanner" title="Escaner">
        <livewire:scanner wire:model.live="product_id"></livewire:scanner>
    </x-modal>
    @endisland

    @island
        <x-modal id="modal-product" title="Producto">
            <livewire:catalog-form></livewire:catalog-form>
        </x-modal>
    @endisland
</div>
