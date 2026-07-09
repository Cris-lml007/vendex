<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Registro de Movimientos</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-inventory" class="btn btn-primary"><i class="fa fa-plus"></i> Añadir Nuevo Lote</button>
    </div>
</x-slot>

@php
    $income = 0;
    $expense = 0;
    $total = 0;
@endphp

<div>
    <div>
        <x-card>
            <livewire:table :heads="$heads">
                @foreach($data as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->product->name }}</td>
                        @if($item->type == \App\Enums\Type::IN)
                            <td>{{ \Illuminate\Support\Number::format($item->price*$item->quantity,2) }}</td>
                            <td>---</td>
                            @php
                                $income+= $item->price*$item->quantity;
                            @endphp
                        @else
                            <td>---</td>
                            <td>{{ \Illuminate\Support\Number::format($item->price*$item->quantity,2) }}</td>
                            @php
                                $expense+= $item->price*$item->quantity;
                            @endphp
                        @endif
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->type->name }}</td>
                        <td>{{ $item->store->name }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-inventory" wire:click="getKardex({{ $item->id }})"><i class="fa fa-eye"></i></button>
                            @if( $item->type != \App\Enums\Type::TRANSFER)
                                <button class="btn btn-danger" wire:click="$js.delete({{ $item->id }})"><i class="fa fa-trash"></i></button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                <livewire:slot name="footer">
                    <tr>
                        <th colspan="2">SUBTOTAL</th>
                        <th>{{ \Illuminate\Support\Number::format($income,2) }}</th>
                        <th>{{ \Illuminate\Support\Number::format($expense,2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="2">TOTAL</th>
                        <th colspan="2" class="text-center">{{ \Illuminate\Support\Number::format($expense-$income,2) }} Bs</th>
                    </tr>
                </livewire:slot>
            </livewire:table>
        </x-card>
    </div>

    @island
    <x-modal id="modal-inventory" title="Inventario" class="modal-lg">
        <livewire:inventory-form></livewire:inventory-form>
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
