<div>
    <form wire:submit="save">
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control" placeholder="Ingrese Nombre" wire:model="name">
                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="">Celular</label>
                    <input type="text" class="form-control" placeholder="Ingrese Celular" wire:model="phone">
                    @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Correo Electronico</label>
                    <input type="email" class="form-control" placeholder="Ingrese Correo Electronico" wire:model="email">
                    @error('email')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="">Direccion</label>
                    <input type="text" class="form-control" placeholder="Ingrese Direccion" wire:model="address">
                    @error('address')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="">Tipo</label>
                    <select class="form-select" wire:model="type">
                        <option value="">Seleccione un Tipo</option>
                        <option value="{{ \App\Enums\Type::STORE }}">{{ \App\Enums\Type::STORE->name  }}</option>
                        <option value="{{ \App\Enums\Type::WAREHOUSE }}">{{ \App\Enums\Type::WAREHOUSE->name }}</option>
                    </select>
                    @error('type')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Estado</label>
                    <select class="form-select" wire:model="status">
                        <option value="">Seleccione un Estado</option>
                        @foreach (App\Enums\Status::cases() as $item)
                            <option value="{{ $item->value }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('status')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @if($edit)
                <div class="row">
                    <div class="col">
                        <h5>En Inventario</h5>
                        <livewire:table :heads="$heads" :searchable="false">
                            @foreach($stock ?? [] as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->pivot->quantity }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>
                                        <a href="{{ route('admin.product.id', $item->id) }}" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </livewire:table>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <h5>Vendidos</h5>
                        <livewire:table :heads="$heads1" :searchable="false">
                            @foreach($sales ?? [] as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->product->name }}</td>
                                    <td>{{ $item->referenceable?->customer?->name ?? '---'}}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ $item->price }}</td>
                                    <td>{{ $item->user->name }}</td>
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
