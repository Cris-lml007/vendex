<div>
    <form wire:submit="save">
        <div class="modal-body">
            <div class="row">
                <div class="col">
                    <label for="">Nombre</label>
                    <input type="text" class="form-control mb-3" wire:model.live="name">
                    <label for="">Origen</label>
                    <input type="text" class="form-control mb-3" wire:model="made">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Color de Titulo</label>
                    <input type="color" class="form-control" wire:model.live="color_text">
                </div>
                <div class="col">
                    <label for="">Color de Fondo</label>
                    <input type="color" class="form-control" wire:model.live="color_bg">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Previzualizacion</label>
                    <div class="d-flex justify-content-center">
                        <h2><strong style="color: {{ $color_text }}; background: {{ $color_bg }}">{{ $name }}</strong></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Guardar</button>
            <button type="reset" data-bs-dismiss="modal" class="btn btn-secondary" wire:click="restart">Cancelar</button>
        </div>
    </form>
</div>
