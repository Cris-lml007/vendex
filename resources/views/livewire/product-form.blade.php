<x-slot name="header">
    <h1>Producto</h1>
</x-slot>

<div>
    <form wire:submit="save">
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col">
                    <label for="">Producto Serializado</label>
                    <select class="form-select" wire:model.live="is_serial">
                        <option value="0">No</option>
                        <option value="1">Si</option>
                    </select>
                </div>
            </div>
                <div class="row mb-3">
                    <div class="col">
                        <label for="">Buscar Producto</label>
                        <input type="text" class="form-control" wire:model.blur.live="search" placeholder="Buscar Producto">
                    </div>
                    <div class="col">
                        <label for="">Producto</label>
                        <select class="form-select" wire:model.live="product_id">
                            <option value="">Seleccione Producto</option>
                            @foreach($products as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control" wire:model="name" placeholder="Ingrese Nombre">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            @if($is_serial == 1)
                <div class="row mb-3">
                    <div class="col">
                        <label for="">Precio Adquisición</label>
                        <div class="input-group">
                            @if($edit)
                                <div class="input-group-text">Bs({{ Number::format($product->kardex()?->first()?->exchange_rate?->usd_to_bs ?? 0,2) }})</div>
                            @else
                                <div class="input-group-text">Bs({{ Number::format(\App\Models\ExchangeRate::orderBy('id','desc')->first()->usd_to_bs,2) }})</div>
                            @endif
                            <input type="number" step="any" class="form-control" wire:model.live="bs1">
                            <div class="input-group-text"><i class="fa fa-share"></i></div>
                            <input type="number" step="any" class="form-control" wire:model.live="usd1">
                            <div class="input-group-text">Usd(1.00)</div>
                        </div>
                        @error('price_purchase')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col">
                        <label for="">Tienda/Almacén</label>
                        <select class="form-select" wire:model="store_id">
                            <option value="">Seleccione Tienda o Almacén</option>
                            @foreach($stores_list ?? [] as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('store_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            @endif
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
                    <div class="input-group">
                        <div class="input-group-text">Bs({{ Number::format(\App\Models\ExchangeRate::orderBy('id','desc')->first()->usd_to_bs,2) }})</div>
                        <input type="text" class="form-control" wire:model.live="bs">
                        <div class="input-group-text"><i class="fa fa-share"></i></div>
                        <input type="text" class="form-control" wire:model.live="usd">
                        <div class="input-group-text">Usd(1.00)</div>
                    </div>

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

            @for($i = 0; $i<$number_labels; $i++)
                <div class="row mb-3">
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Ingrese Etiqueta" wire:model="labels[{{$i}}]">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" placeholder="Ingrese Valor" wire:model="values[{{$i}}]">
                    </div>
                    <div class="col-1">
                        <button type="button" class="btn btn-danger" wire:click="removeTag({{$i}})"><i class="fa fa-trash"></i></button>
                    </div>
                </div>

            @endfor
            <div class="row mb-3">
                <div class="col">
                    <button type="button" wire:click="addLabel" class="btn btn-primary w-100">Añadir Etiqueta</button>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label>Imagen de Producto</label>
                    @if($photo)
                        {{-- Vista previa de la nueva imagen --}}
                        <div class="d-flex justify-content-center" style="height: 300px;">
                            <img src="{{ $photo->temporaryUrl() }}" class="img-thumbnail">
                        </div>
                    @elseif($photo_url)
                        {{-- Imagen guardada --}}
                        <div class="d-flex justify-content-center" style="height: 300px;">
                            <img src="{{ $photo_url }}" class="img-thumbnail">
                        </div>
                    @else
                        {{-- Sin imagen --}}
                        <div class="border rounded p-5 text-center">
                            Sin imagen
                        </div>
                    @endif
                    <input type="file" class="form-control" wire:model="photo" id="photo">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="">Barcode</label>
                    <div class="input-group" >
                        <input type="text" class="form-control" wire:model.live="barcode" placeholder="Ingrese Barcode" @if($edit) readonly @endif>
                        <button type="button" data-bs-target="#modal-scanner" data-bs-toggle="modal" class="btn btn-primary input-group-text" @if($edit) disabled @endif><i class="fa fa-qrcode"></i></button>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div>
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
                <div class="row mb-3">
                    <div class="col">
                        <label for="">Cantidad</label>
                        <input type="number" class="form-control mb-3" placeholder="Ingrese Cantidad" wire:model="tags">
                        <div class="d-flex justify-content-center mb-3">
                            <button type="button" class="btn btn-success" wire:click="generatePdf()">Generar Etiquetas</button>
                        </div>
                    </div>
                </div>
            @if(!$product->is_serialize)
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
                                            <th>Stock Minimo</th>
                                            <th>Cantidad</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($stores ?? [] as $item)
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ __('messages.'.$item->type->name) }}</td>
                                                <td>
                                                    <input wire:blur="setMinQuantity({{$item->id}}, $event.target.value)" type="number" class="form-control" value="{{ $min_quantity[$item->id]}}">
                                                </td>
                                                <td>
                                                    <input wire:blur="setStock({{$item->id}}, $event.target.value)" type="number" @class(['form-control', 'bg-warning' => $min_quantity[$item->id] >= $stocks[$item->id] ?? 0])  value="{{ $stocks[$item->id]}}"/>
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

                            <tr>
                                <td colspan="2">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                        <tr>
                                            <th colspan="4" class="text-center"><strong>HEREDADOS</strong></th>
                                        </tr>
                                        <tr>
                                            <th>Id</th>
                                            <th>Nombre</th>
                                            <th>Precio</th>
                                            <th>Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($product->children()->where('is_serialize',false)->where('status',\App\Enums\Status::ACTIVE)->get() as $item)
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ Number::format($item->price,2) }}</td>
                                                <td>
                                                    <a href="{{ route('admin.product.id', $item->id) }}" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>


                            <tr>
                                <td colspan="2">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                        <tr>
                                            <th colspan="5" class="text-center"><strong>SERIALIZADOS</strong></th>
                                        </tr>
                                        <tr>
                                            <th>Id</th>
                                            <th>Nombre</th>
                                            <th>Precio</th>
                                            <th>Locación</th>
                                            <th>Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($product->children()->where('is_serialize',true)->where('status',\App\Enums\Status::ACTIVE)->get() as $item)
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ Number::format($item->price,2) }}</td>
                                                <td>
                                                    <select class="form-select" wire:model="product_serials[{{$item->store_id}}]">
                                                        <option value="{{ $item->store_id }}">{{ $item?->store?->name ?? '' }}</option>
                                                        @foreach($stores as $item1)
                                                            <option value="{{ $item1->id }}">{{ $item1->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.product.id', $item->id) }}" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>SUBTOTAL</strong></td>
                                <td>{{ $this->product->children()->where('is_serialize',true)->where('status',\App\Enums\Status::ACTIVE)
                        ->whereNotExists(function ($query){
                            $query->select(DB::raw(1))
                                ->from('detail_transactions')
                                ->where('product_id',$this->product->id);
                        })->count() }}</td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <th>TOTAL</th>
                            <th>{{ $total + $this->product->children()->where('is_serialize',true)->where('status',\App\Enums\Status::ACTIVE)
                        ->whereNotExists(function ($query){
                            $query->select(DB::raw(1))
                                ->from('detail_transactions')
                                ->where('product_id',$this->product->id);
                        })->count() }}</th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endif
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
                @if($product->details()->count() < 1 && $product->kardex()->count() < 1)
                    <button wire:click="remove" type="button" class="btn btn-danger me-1">Eliminar</button>
                @endif
                <a href="{{route('admin.products')}}" class="btn btn-secondary">Cerrar</a>
            </div>
        @endif
    </form>
</div>

@script
    <script>
        const input = document.querySelector('#photo');

        input.addEventListener('change', (evento) => {
            const archivo = evento.target.files[0];
            if (!archivo) return;

            const lector = new FileReader();
            lector.readAsDataURL(archivo);

            lector.onload = (e) => {
                const imagen = new Image();
                imagen.src = e.target.result;

                imagen.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // Ajusta el ancho y alto máximo deseado
                    const anchoMaximo = 800;
                    const escala = anchoMaximo / imagen.width;
                    canvas.width = anchoMaximo;
                    canvas.height = imagen.height * escala;

                    // Dibuja la imagen redimensionada en el canvas
                    ctx.drawImage(imagen, 0, 0, canvas.width, canvas.height);

                    // Convierte el canvas a un Blob comprimido (calidad 0.7 = 70%)
                    canvas.toBlob((blob) => {
                        const imagenOptimizado = new File([blob], archivo.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now(),
                        });
                        console.log('Imagen lista:', imagenOptimizado);
                        @this.upload('photo', imagenOptimizado);
                    }, 'image/jpeg', 0.7);
                };
            };
        });

    </script>
@endscript
