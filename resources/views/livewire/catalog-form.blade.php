<div>
    <div class="modal-body">
        <div class="row mb-3">
            <div class="col">
                <label for="">Nombre</label>
                <input type="text" class="form-control" wire:model="name" placeholder="Ingrese Nombre" disabled>
                @error('name')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Marca</label>
                <input type="text" class="form-control" wire:model="brand" disabled>
                @error('brand')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col">
                <label for="">Modelo</label>
                <input type="text" class="form-control" wire:model="model" placeholder="Ingrese Modelo" disabled>
                @error('model')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Precio (Bs)</label>
                <input type="text" class="form-control" wire:model="price" placeholder="Ingrese Precio" disabled>
                @error('price')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="col">
                <label for="">Categoria</label>
                <input type="text" class="form-control" wire:model="category" disabled>
                @error('category')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Descripción</label>
                <textarea class="form-control" rows="3" wire:model="description" placeholder="Ingrese Descripción" disabled></textarea>
                @error('description')
                <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button data-bs-dismiss="modal" class="btn btn-secondary">Cerrar</button>
    </div>
</div>
