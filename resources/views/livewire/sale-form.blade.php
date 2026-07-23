<div>
    <div class="modal-body">
        <div class="row mb-3">
            <div class="col">
                <label for="">CI</label>
                <input type="text" class="form-control" value="{{ $customer->ci ?? '' }}" disabled>
            </div>
            <div class="col">
                <label for="">Nombre Completo</label>
                <input type="text" class="form-control" value="{{ $customer->name ?? '' }}" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Celular</label>
                <input type="text" class="form-control" value="{{ $customer->phone ?? '' }}" disabled>
            </div>
            <div class="col">
                <label for="">Correo Electronico</label>
                <input type="email" class="form-control" value="{{ $customer->email ?? '' }}" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Fecha</label>
                <input type="datetime-local" class="form-control" value="{{ $transaction->created_at ?? '' }}" disabled>
            </div>
            <div class="col">
                <label for="">Vendedor</label>
                <input type="text" class="form-control" value="{{ $user->name ?? '' }}" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <h5>Detalle de Venta</h5>
                <table class="table table-striped">
                    <thead>
                    <td>Producto</td>
                    <td>Cantidad</td>
                    <td>Precio</td>
                    <td>Subtotal</td>
                    </thead>
                    <tbody>
                    @foreach($transaction->details ?? [] as $item)
                        <tr>
                            <td><a href="{{ route('admin.product.id', $item->product->id) }}">{{ $item->product->name }}@if($item->product->is_serialize)({{ $item->product->id }})@endif</a> </td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ Number::format($item->price,2) }}</td>
                            <td>{{ Number::format($item->subtotal,2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <th colspan="3">TOTAL</th>
                    <th>{{ Number::format($transaction->total ?? 0,2) }} Bs</th>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a class="btn btn-primary" href="{{ route('admin.sell.id',$transaction->id ?? 9999999) }}">Generar Recibo</a>
        <button wire:click="$js.delete()" class="btn btn-danger" @if(!$verify) disabled @endif >Eliminar</button>
        <button data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
    </div>
</div>

@script
<script>
    this.$js.delete = () => {
        $("#modal-sale").modal("hide");
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
                let r = await $wire.remove(result.value)
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
