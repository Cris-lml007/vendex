<div>
    <form wire:submit="save">
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col">
                    <label for="">Codigo</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Ingrese Codigo de Producto"
                            wire:model="_id" @if($kardex?->id != null) disabled @endif>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#modal-scanner" class="btn btn-secondary" @if($kardex?->id != null) disabled @endif><i class="fa fa-qrcode"></i></button>
                    </div>
                    @error('_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Producto</label>
                    <select class="form-select" wire:model="_id" @if($kardex?->id != null) disabled @endif>
                        <option value="">Seleccione un Producto</option>
                        @foreach ($products as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">cantidad</label>
                    <input id="quantity" type="number" class="form-control" placeholder="Ingrese Cantidad"
                        wire:model.live="quantity" @if($kardex?->id != null) disabled @endif>
                    @error('quantity')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Precio de Adquisición (Unidad)</label>
                    <input type="text" class="form-control" placeholder="Ingrese Precio de Adquisición"
                        wire:model="price" @if($kardex?->id != null) disabled @endif>
                    @error('price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @if($kardex?->id != null)
                <div class="row mb-3">
                    <div class="col">
                        <label for="">Tienda/Almacen</label>
                        <input type="text" class="form-control" value="{{ $store_name }}" disabled>
                    </div>
                    <div class="col">
                        <label for="">Tipo</label>
                        <select wire:model="store_type" class="form-select" disabled>
                            <option value="{{ \App\Enums\Type::STORE }}">{{ __('messages.'.\App\Enums\Type::STORE->name) }}</option>
                            <option value="{{ \App\Enums\Type::WAREHOUSE }}">{{ __('messages.'.\App\Enums\Type::WAREHOUSE->name) }}</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="">Creado Por</label>
                        <input type="text" value="{{ $kardex?->user?->name ?? '' }}" class="form-control" disabled>
                    </div>
                    <div class="col">
                        <label for="">Fecha</label>
                        <input type="datetime-local" class="form-control" value="{{ $kardex?->created_at ?? ''}}" disabled>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col">
                        <label for="">Tiendas y Almacenes</label>
                        <livewire:table :heads="$heads" wire:model.live="actions" :searchable="false">
                            @foreach ($stores as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ __('messages.'.$item->type->name) }}</td>
                                    <td style="width: 200px;">
                                        <input type="number" class="form-control store-stock"
                                               placeholder="{{ ((int) $quantity - (int) $total) ?? 0 }}"
                                               wire:blur="setStock({{ $item->id }}, $event.target.value)">
                                    </td>
                                </tr>
                            @endforeach
                            <livewire:slot name="footer">
                                <th colspan="2" class="text-center">TOTAL</th>
                                <th @class(['text-danger' => $total != $quantity, 'text-success' => $total == $quantity])>{{ $total }}</th>
                            </livewire:slot>
                        </livewire:table>
                        @error('total')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" @if($kardex?->id != null) disabled @endif>Guardar</button>
            <button data-bs-dismiss="modal" type="reset" class="btn btn-secondary" wire:click="restart">Cancelar</button>
        </div>
    </form>
</div>

@script
<script>

</script>
@endscript
