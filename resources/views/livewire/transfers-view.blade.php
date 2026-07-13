<x-slot name="header">
    <div class="d-flex justify-content-between">
        <h1>Transferencias</h1>
    </div>
</x-slot>

<div>
    <x-card>
        <livewire:table :heads="$heads" wire:model.live="list">
            @foreach($data as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->details()->count()}}</td>
                    <td>
                        <button data-bs-toggle="modal" data-bs-target="#modal-transfer" wire:click="getTransfer({{ $item->id }})" class="btn btn-primary"><i class="fa fa-eye"></i></button>
                    </td>
                </tr>
            @endforeach
        </livewire:table>
        {{ $data->links() }}
    </x-card>

    @island
    <x-modal id="modal-transfer" title="Transferencia" class="modal-lg">
        <livewire:transfer-form></livewire:transfer-form>
    </x-modal>
    @endisland
</div>
