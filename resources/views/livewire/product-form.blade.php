<x-slot name="header">
    <h1>Producto</h1>
</x-slot>

<div>
    <form wire:submit="save">
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control" wire:model="name" placeholder="Ingrese Nombre">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Marca</label>
                    <select class="form-select" wire:model="brand">
                        <option value="">Seleccione Marca</option>
                        @foreach($brands as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('brand')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Modelo</label>
                    <input type="text" class="form-control" wire:model="model" placeholder="Ingrese Modelo">
                    @error('model')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Precio (Bs)</label>
                    <input type="text" class="form-control" wire:model="price" placeholder="Ingrese Precio">
                    @error('price')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Categoria</label>
                    <select class="form-select" wire:model="category">
                        <option value="">Seleccione una Categoria</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Descripción</label>
                    <textarea class="form-control" rows="3" wire:model="description" placeholder="Ingrese Descripción"></textarea>
                    @error('description')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="">Barcode</label>
                    <div class="input-group" >
                        <input type="text" class="form-control" wire:model.live="barcode" placeholder="Ingrese Barcode">
                        <button type="button" data-bs-target="#modal-scanner" data-bs-toggle="modal" class="btn btn-primary input-group-text"><i class="fa fa-qrcode"></i></button>
                    </div>
                    <div class="d-flex justify-content-center mb-3">
                        <img class="img-fluid img-thumbnail" src="{{ $barcode_img }}" alt="Barcode">
                    </div>
                    @error('barcode')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @if($edit)
                <h5>Generar Etiquetas</h5>
                <div class="row">
                    <div class="col">
                        <label for="">Cantidad</label>
                        <input type="number" class="form-control mb-3" placeholder="Ingrese Cantidad" wire:model="tags">
                        <div class="d-flex justify-content-center mb-3">
                            <button type="button" class="btn btn-success" wire:click="generatePdf()">Generar Etiquetas</button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between my-3">
                    <h5>En Inventario</h5>
                </div>
                <div class="row">
                    <div class="col">
                        <table class="table table-striped">
                            <thead>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            </thead>
                            <tbody>
                            @foreach($stores ?? [] as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->type->name }}</td>
                                    <td>
                                        <input wire:blur="setStock({{$item->id}}, $event.target.value)" type="number" class="form-control" value="{{ $stocks[$item->id]}}"/>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            <th colspan="2">TOTAL</th>
                            <th @class(['text-success', 'text-danger' => $total != $total_origin])>{{ $total }}</th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
        </div>
        @if(!$edit)
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Guargar</button>
                <button data-bs-dismiss="modal" type="reset" class="btn btn-secondary">Cancelar</button>
            </div>
        @else
            <div class="d-flex justify-content-end mb-3">
                <button type="submit" class="btn btn-primary me-1">Guargar</button>
                <a href="{{route('admin.products')}}" class="btn btn-secondary">Cerrar</a>
            </div>
        @endif
    </form>
</div>
