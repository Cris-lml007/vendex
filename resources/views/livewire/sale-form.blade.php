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
                            <td>{{ $item->product->name }}</td>
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
        <button class="btn btn-primary">Generar Recibo</button>
        <button wire:click="remove" class="btn btn-danger" @if(!$verify) disabled @endif >Eliminar</button>
        <button data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
    </div>
</div>
