<x-slot name="header">
    <div class="container-fluid">
        <h1>Registrar Venta</h1>
    </div>
</x-slot>

<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Cliente</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">CI/NIT</label>
                            <input type="text" class="form-control" placeholder="CI/NIT" wire:model.live="ci"/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" placeholder="Ingrese Nombre" wire:model="name" @if($customer_id)disabled @endif/>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" placeholder="Ingrese Email" wire:model="email" @if($customer_id)disabled @endif/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" placeholder="Ingrese Celular" wire:model="phone" @if($customer_id)disabled @endif/>
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- PRODUCTOS --}}
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <h5 class="mt-2">Agregar Producto</h5>
                            <select class="form-select w-50" wire:model.live="store" @if(Auth::user()->role == \App\Enums\Role::SELLER) disabled @endif>
                                <option value="">Seleccione Tienda</option>
                                @foreach($stores as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-md-5">
                                <label class="form-label">Producto</label>
                                <div class="input-group">
                                    <select name="" id="" class="form-select" wire:model.live="product_id">
                                        <option value="">Seleccione Producto</option>
                                        @foreach($products ?? [] as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <button data-bs-toggle="modal" data-bs-target="#modal-scanner" class="btn btn-primary"><i class="fa fa-qrcode"></i></button>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Cantidad</label>
                                <input type="number" min="1" class="form-control" wire:model="quantity" placeholder="{{ $product_quantity ?? 'Ingrese Cantidad' }}">
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Precio</label>
                                <input type="number" step="0.01" class="form-control" wire:model="price" placeholder="{{ $product_price ?? 'Ingrese Precio' }}">
                            </div>

                            <div class="col-md-3">
                                <button wire:click="addProduct" class="btn btn-primary w-100">Agregar</button>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- DETALLE --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Detalle de Venta</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">

                            <table class="table table-hover mb-0">

                                <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Producto</th>
                                    <th width="120">Cantidad</th>
                                    <th width="140">Precio</th>
                                    <th width="140">Subtotal</th>
                                    <th width="80"></th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($list ?? [] as $item => $value)
                                    <tr>
                                        <td>{{ $item+1 }}</td>
                                        <td>{{ $value['name'] }}</td>
                                        <td>{{ $value['quantity'] }}</td>
                                        <td>{{ Number::format($value['price'],2) }}</td>
                                        <td>{{ Number::format($value['quantity']*$value['price'],2) }}</td>
                                        <td>
                                            <button
                                                class="btn btn-danger btn-sm" wire:click="removeProduct({{ $item }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="card-footer">

                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6 text-end">
                                <h5 class="mb-2 text-light">
                                    Total: {{ \Illuminate\Support\Number::format($total,2) }} Bs
                                </h5>
                                <div>
                                    <button
                                        class="btn btn-success btn-lg" wire:click="save">
                                        Registrar Venta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-modal id="modal-scanner" title="Scanner">
        <livewire:scanner wire:model.live="product_id"></livewire:scanner>
    </x-modal>
</div>
