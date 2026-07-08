<div>
    <form wire:submit="save">
        <div class="modal-body">
            <div class="row">
                <div class="col">
                    <label for="">CI</label>
                    <input type="text" class="form-control" placeholder="Ingrese CI" wire:model="ci">
                    @error('ci')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Nombre Completo</label>
                    <input type="text" class="form-control" placeholder="Ingrese Nombre Completo" wire:model="name">
                    @error('name')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="">Celular</label>
                    <input type="text" class="form-control" placeholder="Ingrese Celular" wire:model="phone">
                    @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Correo Electronico</label>
                    <input type="email" class="form-control" placeholder="Ingrese Correo Electronico" wire:model="email">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="reset" class="btn btn-secondary">Cancelar</button>
        </div>
    </form>
</div>
