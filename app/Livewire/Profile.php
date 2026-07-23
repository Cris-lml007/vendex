<?php

namespace App\Livewire;

use App\Enums\Type;
use App\Models\Attendance;
use Carbon\Carbon;
use Livewire\Component;

class Profile extends Component
{
    public $user;
    public $lat;
    public $lng;

    public function entry()
    {
        $exists = Attendance::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->where('type', Type::IN)
            ->exists();

        if ($exists) {
            $this->js("
            Swal.fire({
                icon: 'warning',
                title: 'Ya registró su ingreso de hoy'
            });
        ");

            return;
        }

        Attendance::create([
            'user_id' => auth()->id(),
            'type' => Type::IN,
        ]);
        $this->redirect(route('admin.profile'));
    }

    public function sale()
    {
        $exists = Attendance::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->where('type', Type::OUT)
            ->exists();

        if ($exists) {
            $this->js("
            Swal.fire({
                icon: 'warning',
                title: 'Ya registró su salida de hoy'
            });
        ");

            return;
        }

        Attendance::create([
            'user_id' => auth()->id(),
            'type' => Type::OUT,
        ]);
        $this->redirect(route('admin.profile'));
    }

    public function render()
    {
        $this->user = auth()->user();
        return view('livewire.profile');
    }
}
