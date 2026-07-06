<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Store;
use App\Models\User;
use Livewire\Component;

class UsersForm extends Component
{
    public $name;
    public $password;
    public $password_confirmation;
    public $username;
    public $phone;
    public $status;
    public $role;
    public $store_id;

    public function save(){
        $this->validate([
            'name' => 'required',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|min:8|same:password',
            'username' => 'required|unique:users,username',
            'phone' => 'required',
            'status' => 'required',
            'role' => 'required',
        ]);

        $user = User::create([
            'name' => $this->name,
            'username' => $this->username,
            'phone' => $this->phone,
            'password' => $this->password,
            'status' => $this->status,
            'role' => $this->role,
            'store_id' => $this->store_id
        ]);

        $this->redirect(route('admin.users'));
    }

    public function render()
    {
        $stores = Store::where('type', Type::STORE)->get();

        return view('livewire.users-form', compact(['stores']));
    }
}
