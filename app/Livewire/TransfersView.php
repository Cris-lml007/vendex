<?php

namespace App\Livewire;

use App\Models\Transfer;
use Livewire\Component;

class TransfersView extends Component
{

    public function getTransfer($id){
        $this->dispatch('getTransfer', $id)->to(TransferForm::class);
    }


    public function render()
    {
        $data = Transfer::all();
        return view('livewire.transfers-view', compact(['data']));
    }
}
