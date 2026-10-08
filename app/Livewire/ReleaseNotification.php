<?php

namespace App\Livewire;

use App\Models\Release;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReleaseNotification extends Component
{
    public ?Release $release = null;

    public bool $show = false;

    public function mount()
    {
        $this->loadRelease();
    }

    protected function loadRelease()
    {
        if (!Auth::check()) {
            return;
        }

        $this->release = Release::query()
            ->where('active', true)
            ->where(function ($query) {
                $query
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            })
            ->whereDoesntHave('users', function ($query) {
                $query->where('users.id', Auth::id());
            })
            ->latest('published_at')
            ->first();

        if ($this->release) {
            $this->show = true;
        }
    }

    public function markAsSeen()
    {
        if (!$this->release || !Auth::check()) {
            return;
        }

        $this->release->users()->syncWithoutDetaching([
            Auth::id() => [
                'seen_at' => now(),
            ],
        ]);

        $this->show = false;

        $this->js("
            $('#releaseNotificationModal').modal('hide');
        ");
    }

    public function render()
    {
        return view('livewire.release-notification');
    }
}
