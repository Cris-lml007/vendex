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
    </div>

    <x-modal id="modal-category" title="Nueva Categoria">
        <livewire:category-form></livewire:category-form>
    </x-modal>
</div>
