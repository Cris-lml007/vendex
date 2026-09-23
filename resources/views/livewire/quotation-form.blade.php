
<div>

    <div class="row">

        {{-- ========================================================= --}}
        {{-- DATOS DE LA PROFORMA --}}
        {{-- ========================================================= --}}

        <div class="col-12">

            <x-card title="Datos de la proforma">

                <div class="row">

                    {{-- Fecha inicial --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label for="valid_from">
                                Fecha de inicio
                            </label>

                            <input
                                type="date"
                                id="valid_from"
                                class="form-control @error('valid_from') is-invalid @enderror"
                                wire:model="valid_from"
                            >

                            @error('valid_from')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                    </div>


                    {{-- Fecha final --}}
                    <div class="col-md-4">

                        <div class="form-group">

                            <label for="valid_to">
                                Fecha de vencimiento
                            </label>

                            <input
                                type="date"
                                id="valid_to"
                                class="form-control @error('valid_to') is-invalid @enderror"
                                wire:model="valid_to"
                            >

                            @error('valid_to')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror

                        </div>

                    </div>

                </div>

            </x-card>

        </div>


        {{-- ========================================================= --}}
        {{-- PRODUCTOS --}}
        {{-- ========================================================= --}}

        <div class="col-12">

            <x-card title="Productos">

                {{-- Buscador --}}
                <div class="row">

                    <div class="col">

                        <div class="form-group">

                            <label for="search">
                                Buscar producto
                            </label>

                            <input
                                type="text"
                                id="search"
                                class="form-control"
                                placeholder="Buscar por código o nombre..."
                                wire:model.live="search"
                            >

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- RESULTADOS DE BÚSQUEDA --}}
                {{-- ================================================= --}}

                @if ($search && $this->products->count())

                    <div class="list-group mb-3">

                        @foreach ($this->products as $product)

                            <button
                                type="button"
                                class="list-group-item list-group-item-action"
                                wire:click="addProduct('{{ $product->id }}')"
                            >

                                <div class="d-flex justify-content-between align-items-center">

                                    <div>

                                        <strong>
                                            {{ $product->name }}
                                        </strong>

                                        @if ($product->id)

                                            <small class="text-muted d-block">
                                                Código: {{ $product->id }}
                                            </small>

                                        @endif

                                    </div>


                                    <div>

                                        Bs.
                                        {{ number_format($product->price, 2) }}

                                    </div>

                                </div>

                            </button>

                        @endforeach

                    </div>

                @elseif ($search)

                    <div class="alert alert-secondary">

                        No se encontraron productos.

                    </div>

                @endif


                {{-- ================================================= --}}
                {{-- TABLA DE PRODUCTOS --}}
                {{-- ================================================= --}}

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>

                            <tr>

                                <th width="10%">
                                    Código
                                </th>

                                <th>
                                    Producto
                                </th>

                                <th width="15%">
                                    Cantidad
                                </th>

                                <th width="15%">
                                    Precio
                                </th>

                                <th width="15%">
                                    Subtotal
                                </th>

                                <th width="5%">
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($details as $index => $detail)

                                <tr>

                                    {{-- Código --}}
                                    <td>
                                        {{ $detail['product_id'] ?? '-' }}
                                    </td>


                                    {{-- Producto --}}
                                    <td>
                                        {{ $detail['name'] }}
                                    </td>


                                    {{-- Cantidad --}}
                                    <td>

                                        <input
                                            type="number"
                                            min="1"
                                            class="form-control"
                                            wire:model.live="details.{{ $index }}.quantity"
                                        >

                                    </td>


                                    {{-- Precio --}}
                                    <td>

                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            class="form-control"
                                            wire:model.live="details.{{ $index }}.price"
                                        >

                                    </td>


                                    {{-- Subtotal --}}
                                    <td class="text-right">

                                        Bs.

                                        {{
                                            number_format(
                                                ($detail['quantity'] ?? 0)
                                                *
                                                ($detail['price'] ?? 0),
                                                2
                                            )
                                        }}

                                    </td>


                                    {{-- Eliminar --}}
                                    <td class="text-center">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            wire:click="removeProduct({{ $index }})"
                                        >

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="6"
                                        class="text-center text-muted py-4"
                                    >

                                        No hay productos agregados a la proforma.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>


                        {{-- ================================================= --}}
                        {{-- TOTAL --}}
                        {{-- ================================================= --}}

                        @if (count($details))

                            <tfoot>

                                <tr>

                                    <th
                                        colspan="4"
                                        class="text-right"
                                    >
                                        Total
                                    </th>

                                    <th class="text-right">

                                        Bs.
                                        {{ number_format($this->total, 2) }}

                                    </th>

                                    <th>
                                    </th>

                                </tr>

                            </tfoot>

                        @endif

                    </table>

                </div>

            </x-card>

        </div>


        {{-- ========================================================= --}}
        {{-- BOTONES --}}
        {{-- ========================================================= --}}

        <div class="col-12">

            <div class="d-flex justify-content-end">

                <a
                    href="{{ route('quotations') }}"
                    class="btn btn-secondary mr-2"
                >
                    Cancelar
                </a>


                <button
                    type="button"
                    class="btn btn-primary"
                    wire:click="save"
                    wire:loading.attr="disabled"
                >

                    <span wire:loading.remove wire:target="save">

                        <i class="fas fa-save"></i>

                        Guardar proforma

                    </span>


                    <span wire:loading wire:target="save">

                        <i class="fas fa-spinner fa-spin"></i>

                        Guardando...

                    </span>

                </button>

            </div>

        </div>

    </div>

</div>
