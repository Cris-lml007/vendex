<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class UsersView extends Component
{
    public function render()
    {
        $data = User::all();
        return view('livewire.users-view', compact('data'));
    }
}
