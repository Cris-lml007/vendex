
<div>
    <x-slot name="header">
        <h1>
            <i class="fas fa-tags mr-2"></i>
            Gestión de precios
        </h1>
    </x-slot>

    <div class="container-fluid">

        {{-- Buscador y acciones --}}
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row align-items-end">

                    <div class="col-lg-6 col-md-12">
                        <label for="search">Buscar producto</label>

                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>

                            <input
                                id="search"
                                type="text"
                                class="form-control"
                                placeholder="Código, nombre, modelo o marca..."
                                wire:model.live.debounce.300ms="search"
                            >

                            @if ($search !== '')
                                <div class="input-group-append">
                                    <button
                                        type="button"
                                        class="btn btn-secondary"
                                        wire:click="$set('search', '')"
                                        title="Limpiar búsqueda"
                                    >
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mt-3 mt-lg-0">
                        <div class="text-lg-center">
                            <span class="text-muted">
                                Productos encontrados
                            </span>

                            <h4 class="mb-0">
                                <span class="badge badge-primary">
                                    {{ $totalProducts }}
                                </span>
                            </h4>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mt-3 mt-lg-0">
                        <button
                            type="button"
                            class="btn btn-success btn-block"
                            wire:click="saveAll"
                            wire:loading.attr="disabled"
                            wire:target="saveAll"
                            @disabled($modifiedCount === 0)
                        >
                            <span
                                wire:loading.remove
                                wire:target="saveAll"
                            >
                                <i class="fas fa-save mr-1"></i>
                                Guardar todos
                                @if ($modifiedCount > 0)
                                    ({{ $modifiedCount }})
                                @endif
                            </span>

                            <span
                                wire:loading
                                wire:target="saveAll"
                            >
                                <i class="fas fa-spinner fa-spin mr-1"></i>
                                Guardando...
                            </span>
                        </button>

                        @if ($modifiedCount > 0)
                            <small class="text-warning d-block text-center mt-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $modifiedCount }}
                                {{ $modifiedCount === 1 ? 'precio pendiente' : 'precios pendientes' }}
                            </small>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{-- Productos agrupados por marca --}}
        @forelse ($groupedProducts as $brandName => $products)

            <div class="card card-outline card-secondary">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tag mr-2"></i>
                        {{ $brandName }}
                    </h3>

                    <div class="card-tools">
                        <span class="badge badge-info">
                            {{ $products->count() }}
                            {{ $products->count() === 1 ? 'producto' : 'productos' }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">

                        <table class="table table-hover table-striped mb-0">

                            <thead>
                                <tr>
                                    <th style="width: 90px;">Código</th>
                                    <th>Producto</th>
                                    <th>Modelo</th>
                                    <th style="min-width: 180px;">
                                        Precio de venta
                                    </th>
                                    <th style="width: 100px;" class="text-center">
                                        Acción
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($products as $product)

                                    @php
                                        $productId = (string) $product->id;

                                        $isModified = isset($prices[$productId])
                                            && isset($originalPrices[$productId])
                                            && number_format((float) $prices[$productId], 2, '.', '')
                                                !== number_format((float) $originalPrices[$productId], 2, '.', '');
                                    @endphp

                                    <tr
                                        wire:key="product-price-{{ $product->id }}"
                                        class="{{ $isModified ? 'table-warning' : '' }}"
                                    >
                                        <td class="align-middle">
                                            <span class="text-muted">
                                                {{ $product->id }}
                                            </span>
                                        </td>

                                        <td class="align-middle">
                                            <strong>
                                                {{ $product->name }}
                                            </strong>
                                        </td>

                                        <td class="align-middle">
                                            {{ $product->model ?: '—' }}
                                        </td>

                                        <td class="align-middle">
                                            <div class="input-group input-group-sm">

                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        Bs.
                                                    </span>
                                                </div>

                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    class="form-control text-right @error('prices.' . $productId) is-invalid @enderror"
                                                    wire:model.blur.live="prices.{{ $productId }}"
                                                    wire:keydown.enter='savePrice("{{ $productId }}")'
                                                >

                                                @error('prices.' . $productId)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror

                                            </div>
                                        </td>

                                        <td class="align-middle text-center">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-success"
                                                wire:click='savePrice("{{ $productId }}")'
                                                wire:loading.attr="disabled"
                                                wire:target='savePrice("{{ $productId }}")'
                                                title="Guardar precio"
                                            >
                                                <span
                                                    wire:loading.remove
                                                    wire:target='savePrice("{{ $productId }}")'
                                                >
                                                    <i class="fas fa-save"></i>
                                                </span>

                                                <span
                                                    wire:loading
                                                    wire:target='savePrice("{{ $productId }}")'
                                                >
                                                    <i class="fas fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>
                </div>
            </div>

        @empty

            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>

                    <h5>No se encontraron productos</h5>

                    <p class="text-muted mb-0">
                        Intenta buscar con otro código, nombre, modelo o marca.
                    </p>
                </div>
            </div>

        @endforelse

    </div>
</div>
