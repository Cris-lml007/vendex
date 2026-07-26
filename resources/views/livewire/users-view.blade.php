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
                <livewire:table :heads="$heads" wire:model.live="list">
                    @foreach($data ?? [] as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ __('messages.'.$item->role->name) }}</td>
                            <td>{{ $item->store->name ?? '---' }}</td>
                            <td>{{ __('messages.'.$item->status->name) }}</td>
                            <td>
                                <button data-bs-toggle="modal" data-bs-target="#modal-users" wire:click="getUser({{ $item->id }})" class="btn btn-primary"><i class="fa fa-eye"></i></button>
                                @php
                                    $user = \App\Models\User::find($item->id);
                                @endphp
                                @if(($user->sales()->count() <= 0 || $user->attendances()->count() <= 0) && $user->id != 1)
                                    <button wire:click="$js.delete({{ $item->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </livewire:table>
                {{ $data->links() }}
            </div>
        </div>
    </x-card>

    @island
    <x-modal id="modal-users" title="" class="modal-lg">
        <livewire:users-form></livewire:users-form>
    </x-modal>
    @endisland
</div>

@script
<script>
    this.$js.delete = (id) => {
        window.Swal.fire({
            icon: "warning",
            title: "Eliminar?",
            text: "Esta seguro que desea eliminar, este proceso puede dañar los registros",
            input: "password",
            confirmButtonText: "Eliminar",
            confirmButtonColor: "gray",
            background: "red",
            color: "white",
        }).then( async (result) => {
            if(result.isConfirmed){
                let r = await $wire.remove(result.value,id)
                console.log(r)
                if(r){
                    window.Swal.fire({
                        title: "Eliminado Correctamente",
                        icon: "success"
                    })
                }else{
                    window.Swal.fire({
                        title: "No se pudo Eliminar",
                        icon: "error"
                    })
                }
            }
        });
    }
</script>
@endscript
