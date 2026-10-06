<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Proformas</h1>
        <a href="{{ route('admin.quotations.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Crear Nueva Proforma</a>
    </div>
</x-slot>

<div>
    <x-card>
        <livewire:table :heads="$heads" wire:model.live="list">
            @foreach ($data as $item)
        <tr style="cursor: pointer;" wire:click="getQuotation({{ $item->id }})">
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->valid_from }}</td>
                    <td>{{ $item->valid_to }}</td>
                    <td>{{ $item->user->name }}</td>
                </tr>
            @endforeach
        </livewire:table>
    </x-card>
</div>
