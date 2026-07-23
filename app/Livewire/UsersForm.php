<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Store;
use App\Models\User;
use Livewire\Attributes\On;
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
    public $entry_time;
    public $exit_time;

    public User $user;

    #[On('getUser')]
    public function getUser($id){
        $user = User::find($id);
        $this->user = $user;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->status = $user->status;
        $this->role = $user->role;
        $this->store_id = $user->store_id;
        $this->phone = $user->phone;
        $this->entry_time = $user->entry_time;
        $this->exit_time = $user->exit_time;
    }

    public function mount()
    {
        $this->user = new User();
    }

    public function save(){
        $this->validate([
            'name' => 'required',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|min:8|same:password',
            'username' => 'required|unique:users,username,'.$this?->user?->id ?? null,
            'phone' => 'required',
            'status' => 'required',
            'role' => 'required',
        ]);

        $user = User::updateOrCreate([
            'username' => $this->username,
        ],[
            'name' => $this->name,
            'phone' => $this->phone,
            'password' => $this->password,
            'status' => $this->status,
            'role' => $this->role,
            'store_id' => $this->store_id,
            'entry_time' => $this->entry_time,
            'exit_time' => $this->exit_time,
        ]);

        $this->redirect(route('admin.users'));
    }

    public function render()
    {
        $stores = Store::where('type', Type::STORE)->get();

        return view('livewire.users-form', compact(['stores']));
    }
}
