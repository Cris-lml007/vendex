<div>
    <form wire:submit="save">
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col">
                    <label for="">Nombre Completo</label>
                    <input type="text" class="form-control" placeholder="Ingrese Nombre Completo" wire:model="name">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Celular</label>
                    <input type="text" class="form-control" placeholder="Ingrese numero de Celular" wire:model="phone">
                    @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Usuario</label>
                    <input type="text" class="form-control" placeholder="Ingrese usuario" wire:model="username">
                    @error('username')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Rol</label>
                    <select name="" id="" class="form-select" wire:model="role">
                        <option value="">Seleccione un Rol</option>
                        @foreach(\App\Enums\Role::cases() as $item)
                            <option value="{{ $item }}">{{ __('messages.'.$item->name) }}</option>
                        @endforeach
                    </select>
                    @error('role')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Tienda</label>
                    <select class="form-select" wire:model="store_id">
                        <option value="">Seleccione Tienda</option>
                        @foreach( $stores ?? [] as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('store_id')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Estado</label>
                    <select name="" id="" class="form-select" wire:model="status">
                        <option value="">Seleccione Estado</option>
                        @foreach(\App\Enums\Status::cases() as $item)
                            @if($item != \App\Enums\Status::SALE)
                                <option value="{{ $item }}">{{ __('messages.'.$item->name) }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('status')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Hora de Ingreso</label>
                    <input type="time" class="form-control" wire:model="entry_time">
                </div>
                <div class="col">
                    <label for="">Hora de Ingreso</label>
                    <input type="time" class="form-control" wire:model="exit_time">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label for="">Contraseña</label>
                    <input type="password" class="form-control" placeholder="Ingrese Contraseña" wire:model="password">
                    @error('password')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col">
                    <label for="">Repetir Contraseña</label>
                    <input type="password" class="form-control" placeholder="Repetir Contraseña" wire:model="password_confirmation">
                    @error('password_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" type="submit">Guardar</button>
            <button class="btn btn-secondary" type="reset" data-bs-dismiss="modal">Cancelar</button>
        </div>
    </form>
</div>
