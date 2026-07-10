<div>
    <div class="modal-body">
        <div class="row mb-3">
            <div class="col">
                <label for="">Creado Por</label>
                <input type="text" class="form-control" value="{{ $user?->name ?? '' }}" disabled>
            </div>
            <div class="col">
                <label for="">Fecha</label>
                <input type="text" class="form-control" value="{{ $date }}" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <table class="table table-striped container-fluid">
                    <thead>
                    <tr>
                        <th class="text-center" colspan="4">Antes</th>
                        <th></th>
                        <th class="text-center" colspan="4">Despues</th>
                    </tr>
                    <tr>
                        <th>Id</th>
                        <th>producto</th>
                        <th>cantidad</th>
                        <th>Tienda/Almacen</th>
                        <th><i class="fa fa-share"></i></th>
                        <th>Id</th>
                        <th>producto</th>
                        <th>cantidad</th>
                        <th>Tienda/Almacen</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($details ?? [] as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->store->name }}</td>
                            <td><i class="fa fa-share"></i></td>
                            <td>{{ $item->kardex->id }}</td>
                            <td>{{ $item->kardex->product->name }}</td>
                            <td>{{ $item->kardex->quantity }}</td>
                            <td>{{ $item->kardex->store->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-danger" wire:click="remove" @if(!$verify) disabled @endif>Eliminar</button>
        <button data-bs-dismiss="modal" class="btn btn-secondary">Cancelar</button>
    </div>
</div>
