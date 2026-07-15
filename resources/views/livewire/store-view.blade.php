<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Tiendas y Almacenes</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-store" class="btn btn-primary"><i class="fa fa-plus"></i> Añadir Nueva Locación</button>
    </div>
</x-slot>

<div>
    <div>
        <x-card>
            <livewire:table :heads="$heads" :searchable="false">
                @foreach ($stores as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ __('messages.'.$item->type->name) }}</td>
                    <td>{{ __('messages.'.$item->status->name) }}</td>
                    <td>
                        <a class="btn btn-primary" href="{{ route('admin.store.id', $item->id) }}"><i class="fa fa-eye"></i></a>
                    </td>
                </tr>
                @endforeach
            </livewire:table>
        </x-card>
    </div>

    <x-modal id="modal-store" title="Tiendas y Almacenes" class="modal-lg">
        <livewire:store-form></livewire:store-form>
    </x-modal>
</div>
