<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Catalogo de Productos</h1>
        <button data-bs-toggle="modal" data-bs-target="#modal-product" class="btn btn-primary"><i class="fa fa-plus"></i>
            Añadir Nuevo Producto</button>
    </div>
</x-slot>

<div>
    <div>
        <x-card>
            <livewire:table :heads="$heads" wire:model.live="list">
                @foreach ($products as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->model }}</td>
                        @foreach (json_decode($settings->product_tags) ?? [] as $tag)
                            <td>{{ $item->tags()->where('name', 'like', $tag)->first()->value ?? '---' }}</td>
                        @endforeach
                        <td>{{ $item->color ?? '' }}</td>
                        <td>{{ $item->is_serialize == 0 ? 'No' : 'Si' }}</td>
                        @if ($item->brand)
                            <td><strong
                                    style="color: {{ $item->brand->color_fg }}; background: {{ $item->brand->color_bg }}">{{ $item->brand->name }}</strong>
                            </td>
                        @else
                            <td><strong>---</strong></td>
                        @endif
                        <td>{{ $item?->category?->name ?? '---' }}</td>

                        @php
                            $total =
                                $item->stocks()->sum('quantity') +
                                $item
                                    ->children()
                                    ->where('is_serialize', true)
                                    ->where('status', \App\Enums\Status::ACTIVE)
                                    ->whereNotExists(function ($query) use ($item) {
                                        $query
                                            ->select(DB::raw(1))
                                            ->from('detail_transactions')
                                            ->where('product_id', $item->id);
                                    })
                                    ->count();
                        @endphp

                        <td>{{ $total }}</td>
                        <td>{{ Number::format($item->price * $rate, precision: 2) }}</td>
                        <td>
                            <a href="{{ route('admin.product.id', $item->id) }}" class="btn btn-primary"><i
                                    class="fa fa-eye"></i></a>
                        </td>
                    </tr>
                @endforeach
            </livewire:table>
            {{ $products->links() }}
        </x-card>
    </div>

    @island
        <x-modal id="modal-product" title="Nuevo Producto" class="modal-lg">
            <livewire:product-form></livewire:product-form>
        </x-modal>
    @endisland

    @island
        <x-modal id="modal-scanner" title="Escaner">
            <livewire:scanner wire:model.live="product_id"></livewire:scanner>
        </x-modal>
    @endisland
</div>
