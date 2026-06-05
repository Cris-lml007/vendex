<div>
    <form wire:submit="save">
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control" placeholder="Ingrese Nombre" wire:model="name">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Tipo</label>
                    <select class="form-select" wire:model="type">
                        <option value="">Seleccione un Tipo</option>
                        @foreach (App\Enums\Type::cases() as $item)
                            <option value="{{ $item->value }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <label for="">Estado</label>
                    <select class="form-select" wire:model="status">
                        <option value="">Seleccione un Estado</option>
                        @foreach (App\Enums\Status::cases() as $item)
                            <option value="{{ $item->value }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary">Guardar</button>
            <button class="btn btn-secondary">Cancelar</button>
        </div>
    </form>
</div>
