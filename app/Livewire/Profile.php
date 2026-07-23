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

    private function isInsideGeofence(): bool {

        if($this->user->store->lat == null || $this->user->store->long == null) {
            return true;
        }

        $earthRadius = 6371000; // metros

        $latFrom = deg2rad($this->user->store->lat);
        $lonFrom = deg2rad($this->user->store->long);

        $latTo = deg2rad($this->lat);
        $lonTo = deg2rad($this->Lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
                pow(sin($latDelta / 2), 2) +
                cos($latFrom) * cos($latTo) *
                pow(sin($lonDelta / 2), 2)
            ));

        $distance = $angle * $earthRadius;

        return $distance <= $this->user->store->radius;
    }


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

        if(!$this->isInsideGeofence()){
            $this->js("
            Swal.fire({
                icon: 'warning',
                title: 'No se puede Registrar'
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

        if(!$this->isInsideGeofence()){
            $this->js("
            Swal.fire({
                icon: 'warning',
                title: 'No se puede Registrar'
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
