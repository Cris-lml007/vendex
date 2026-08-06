<div>
    <div class="modal-body">
        <div class="row mb-3">
            <div class="col">
                <label for="">Codigo</label>
                <input class="form-control" type="text" disabled value="{{ $product->id ?? '' }}">
            </div>
            @if($product?->is_serialize)
                <div class="col">
                    <label for="">Tienda</label>
                    <select wire:model.live="store_id" class="form-select">
                        @foreach($stores ?? [] as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Nombre</label>
                <input type="text" class="form-control" wire:model="name" placeholder="Ingrese Nombre" disabled>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Marca</label>
                <input type="text" class="form-control" wire:model="brand" disabled>
                @error('brand')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col">
                <label for="">Modelo</label>
                <input type="text" class="form-control" wire:model="model" placeholder="Ingrese Modelo" disabled>
                @error('model')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Precio (Bs)</label>
                <input type="text" class="form-control" wire:model="price" placeholder="Ingrese Precio" disabled>
                @error('price')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Categoria</label>
                <input type="text" class="form-control" wire:model="category" disabled>
                @error('category')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col">
                <label for="">Color</label>
                <input type="text" class="form-control" wire:model="color" disabled>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Descripción</label>
                <textarea class="form-control" rows="3" wire:model="description" placeholder="Ingrese Descripción" disabled></textarea>
                @error('description')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        @foreach($product->tags ?? [] as $item)
            <div class="row mb-3">
                <div class="col">
                    <input type="text" class="form-control" value="{{ $item->name }}" disabled >
                </div>
                <div class="col">
                    <input type="text" class="form-control" value="{{ $item->value }}" disabled >
                </div>
            </div>
        @endforeach

        @if(!$product?->is_serialize)
            <div class="row">
                <div class="col">
                    <table class="table table-striped">
                        <thead>
                        <th colspan="2" class="text-center"><strong>EN INVENTARIO</strong></th>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="2">
                                <table class="table mb-0">
                                    <thead>
                                    <tr>
                                        <th colspan="4" class="text-center"><strong>NO SERIALIZADOS</strong></th>
                                    </tr>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($stores ?? [] as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ __('messages.'.$item->type->name) }}</td>
                                            <td>
                                                <input wire:blur="setStock({{$item->id}}, $event.target.value)" type="number" @class(['form-control'])  value="{{ $stocks[$item->id]}}"/>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>SUBTOTAL</strong></td>
                            <td><strong @class(['text-success', 'text-danger' => $total != $total_origin])>{{ $total }}</strong></td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <th>TOTAL</th>
                        <th>{{ $total }}</th>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    </div>
    <div class="modal-footer">
        <button wire:click="transfer()" class="btn btn-primary" @if($stocks === $stocks_cp && $product?->store_id == $store_id) disabled @endif>Transferir</button>
        <button data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
    </div>
</div>
