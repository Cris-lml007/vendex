<div>
    <form wire:submit="save">
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col">
                    <label for="">Codigo</label>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Ingrese Codigo de Producto"
                            wire:model="_id">
                        <button class="btn btn-secondary"><i class="fa fa-qrcode"></i></button>
                    </div>
                    @error('_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Producto</label>
                    <select class="form-select" wire:model="_id">
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
                        wire:model.live="quantity">
                    @error('quantity')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Precio de Adquisición (Unidad)</label>
                    <input type="text" class="form-control" placeholder="Ingrese Precio de Adquisición"
                        wire:model="price">
                    @error('price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="">Tiendas y Almacenes</label>
                    <livewire:table :heads="['Nombre', 'Tipo', 'Cantidad']">
                        @foreach ($stores as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->type->name }}</td>
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
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button data-bs-dismiss="modal" type="reset" class="btn btn-secondary">Cancelar</button>
        </div>
    </form>
</div>

@script
<script>

</script>
@endscript
