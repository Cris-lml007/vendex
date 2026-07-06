<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Usuarios</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-users" class="btn btn-primary"><i class="fa fa-plus"></i> Añadir Nuevo Usuario</button>
    </div>
</x-slot>
<div>
    <x-card>
        <div class="row">
            <div class="col">
                <livewire:table :heads="['ID','Nombre','Rol', 'Tienda', 'Estado','Opciones']">
                    @foreach($data ?? [] as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->role->name }}</td>
                            <td>{{ $item->store->name ?? '---' }}</td>
                            <td>{{ $item->status->name }}</td>
                            <td>
                                <button class="btn btn-primary"><i class="fa fa-eye"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </livewire:table>
            </div>
        </div>
    </x-card>

    <x-modal id="modal-users" title="" class="modal-lg">
        <livewire:users-form></livewire:users-form>
    </x-modal>
</div>
