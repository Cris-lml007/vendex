<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Catalogo</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-scanner" class="btn btn-primary"><i class="fa fa-qrcode"></i> Buscar por Codigo de Barras</button>
    </div>
</x-slot>

<div>
    <style>
        .product-image{
            height:180px;
            background:#f8f9fa;
        }

        .card{
            transition:.2s;
        }

        .card:hover{
            transform:translateY(-3px);
            box-shadow:0 .5rem 1rem rgba(0,0,0,.15);
        }
    </style>
    <div class="d-flex flex-column min-vh-100">
        <div class="flex-grow-1">
            <div class="container">
                <div class="d-flex justify-content-end mb-3">
                    <input type="text" class="form-control w-25" placeholder="Buscar..." wire:model.live="search">
                </div>
                <div class="row g-3">

                    @foreach($data ?? [] as $product)

                        <div class="col-6 col-md-4 col-lg-3 col-xl-3">

                            <div class="card h-100 shadow-sm">

                                @if(\Illuminate\Support\Facades\Storage::disk('local')->exists("products/{$product->id}.jpg"))
                                    <img
                                        src="{{ 'data:image/png;base64,'.base64_encode(\Illuminate\Support\Facades\Storage::disk('local')->get("products/{$product->id}.jpg"))}}"
                                        class="card-img-top product-image"
                                        alt="{{ $product->name }}">
                                @else
                                    <div class="card-img-top product-image text-center p-5 border bg-gray">
                                        Sin Imagen
                                    </div>
                                @endif
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title text-truncate">
                                        <strong>{{ $product->name }}</strong>
                                    </h6>
                                    <div class="badge" style="background: {{ $product->brand->color_bg }};color: {{ $product->brand->color_fg }};">
                                        {{ $product->brand->name }}
                                    </div>
                                    <h5 class="text-success mt-2">
                                        <strong>Bs {{ number_format($product->price,2) }}</strong>
                                    </h5>
                                    <div class="mt-auto">
                                        <button wire:click="getProduct('{{ $product->id }}')" data-bs-toggle="modal" data-bs-target="#modal-product" class="btn btn-primary w-100">
                                            Ver
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="border-top py-3">
            <div class="container d-flex justify-content-end">
                {{ $data->links() }}
            </div>
        </div>
    </div>

    @island
    <x-modal id="modal-scanner" title="Escaner">
        <livewire:scanner wire:model.live="product_id"></livewire:scanner>
    </x-modal>
    @endisland

    @island
        <x-modal id="modal-product" title="Producto">
            <livewire:catalog-form></livewire:catalog-form>
        </x-modal>
    @endisland
</div>
