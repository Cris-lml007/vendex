<div>
    <form wire:submit="save">
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control" placeholder="Ingrese Nombre" wire:model="name">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Tipo</label>
                    <select class="form-select" wire:model="type">
                        <option value="">Seleccione un Tipo</option>
                        @foreach (App\Enums\Type::cases() as $item)
                            <option value="{{ $item->value }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label for="">Estado</label>
                    <select class="form-select" wire:model="status">
                        <option value="">Seleccione un Estado</option>
                        @foreach (App\Enums\Status::cases() as $item)
                            <option value="{{ $item->value }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if($edit)
                <div class="row">
                    <div class="col">
                        <h5>En Inventario</h5>
                        <livewire:table :heads="['Nombre','Disponibles','Precio','Acciones']">
                            @foreach($stock ?? [] as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->pivot->quantity }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>
                                        <a href="{{ route('admin.product.id', $item->id) }}" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                        <button class="btn btn-secondary"><i class="fa fa-lock"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </livewire:table>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <h5>Vendidos</h5>
                        <livewire:table :heads="['Id','Producto','Cliente','Cantidad','Precio','Por']">
                            @foreach($sales ?? [] as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ '' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </livewire:table>
                    </div>
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button data-bs-dismiss="modal" type="reset" class="btn btn-secondary">Cancelar</button>
        </div>
    </form>
</div>
