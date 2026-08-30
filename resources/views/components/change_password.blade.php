<?php

use Livewire\Component;

new class extends Component {
    public $password_current;
    public $password;
    public $password_verify;

    public function change_password()
    {
        $this->validate(
            [
                'password_current' => 'required|current_password',
                'password' => 'required|min:8',
                'password_verify' => 'required|confirmed:password',
            ],
            [],
            [
                'password_current' => 'contraseña actual',
                'password_verify' => 'contraseña',
            ],
        );

        Auth::user()->update(['password' => Hash::make($this->password)]);
        $this->redirect(route('admin.profile'));
    }
};
?>

<div>
    <div class="modal-body">
        <div class="row mb-3">
            <div class="col">
                <label for="">Contraseña Actual</label>
                <input type="password" class="form-control" placeholder="Ingrese Contraseña Actual"
                    wire:model="password_current">
                @error('password_current')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Contraseña Nueva</label>
                <input type="password" class="form-control" placeholder="Ingrese Contraseña Nueva"
                    wire:model="password">
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="">Repetir Contraseña</label>
                <input type="password" class="form-control" placeholder="Ingrese Contraseña Nueva"
                    wire:model="password_verify">
                @error('password_verify')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button wire:click="change_password" type="button" class="btn btn-primary">Guardar</button>
        <button type="button" data-bs-toggle="modal" data-bs-dismiss="#modal-password"
            class="btn btn-secondary">Cancelar</button>
    </div>
</div>

