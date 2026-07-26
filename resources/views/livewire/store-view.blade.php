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
                        @php
                            $store = \App\Models\Store::find($item->id);
                        @endphp
                        @if($store->transactions()->count() <= 0 || $store->products()->count() <= 0 || $store->products_serial()->count() <= 0)
                            <button wire:click="$js.delete({{ $item->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></button>
                        @endif
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
