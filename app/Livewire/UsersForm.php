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
        $rule = [
            'name' => 'required',
            'password_confirmation' => 'same:password',
            'username' => 'required|unique:users,username,'.$this?->user?->id ?? null,
            'phone' => 'required',
            'status' => 'required',
            'role' => 'required',
        ];

        if($this?->user?->id == null){
            $rule['password'] = 'required|min:8';
        }

        $this->validate($rule);

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'status' => $this->status,
            'role' => $this->role,
            'store_id' => $this->store_id,
            'entry_time' => $this->entry_time,
            'exit_time' => $this->exit_time,
        ];

        if($this->password != '' && $this->password_confirmation == $this->password){
            $data['password'] = $this->password;
        }

        $user = User::updateOrCreate([
            'username' => $this->username,
        ],$data);

        $this->redirect(route('admin.users'));
    }

    public function render()
    {
        $stores = Store::where('type', Type::STORE)->get();

        return view('livewire.users-form', compact(['stores']));
    }
}
