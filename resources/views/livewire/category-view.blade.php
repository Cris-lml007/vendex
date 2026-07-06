<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Categorias</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-category" class="btn btn-primary"><i class="fa fa-plus"></i>
            Añadir Nueva Categoria</button>
    </div>
</x-slot>


<div>
    <div>
        <x-card>
            <livewire:table :heads="$heads">
                @foreach ($categories as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>
                            <button data-bs-toggle="modal" data-bs-target="#modal-category"
                                wire:click="getCategory({{ $item->id }})" class="btn btn-primary"><i
                                    class="fa fa-eye"></i></button>
                        </td>
                    </tr>
                @endforeach
            </livewire:table>
        </x-card>
        <div class="d-flex justify-content-between my-3">
            <h2>Marcas</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-brand"><i class="fa fa-plus"></i> Añadir Nueva Marca</button>
        </div>
        <x-card>
            <livewire:table :heads="['Id','Nombre','Origen','Acciones']">
                @foreach ($brands as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td><strong style="color: {{ $item->color_fg }}; background: {{ $item->color_bg }}">{{ $item->name }}</strong></td>
                        <td>{{ $item->made }}</td>
                        <td>
                            <button data-bs-toggle="modal" data-bs-target="#modal-brand"
                                    wire:click="getBrand({{ $item->id }})" class="btn btn-primary"><i
                                    class="fa fa-eye"></i></button>
                        </td>
                    </tr>
                @endforeach
            </livewire:table>
        </x-card>
    </div>
    @island
        <x-modal id="modal-category" title="Nueva Categoria">
            <livewire:category-form></livewire:category-form>
        </x-modal>
    @endisland

    @island
        <x-modal id="modal-brand" title="Nueva Marca">
            <livewire:brand-form></livewire:brand-form>
        </x-modal>
    @endisland
</div>
