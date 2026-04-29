<div>
    <form wire:submit="save">
        <div class="modal-body">
            <label for="">Nombre</label>
            <input type="text" class="form-control" placeholder="Ingrese Categoria" wire:model="name">
            @error('name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="reset" data-bs-dismiss="modal" class="btn btn-secondary">Cancelar</button>
        </div>
    </form>
</div>
