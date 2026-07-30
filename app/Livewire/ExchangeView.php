<?php

namespace App\Livewire;

use App\Models\ExchangeRate;
use Livewire\Component;

class ExchangeView extends Component
{

    public $bs = 1;

    public function save(){
        ExchangeRate::create([
            'usd_to_bs' => $this->bs,
        ]);
    }


    public function render()
    {
        $this->bs = ExchangeRate::orderBy('id','desc')?->first()?->usd_to_bs ?? 1;
        $exchange_rates = ExchangeRate::all();
        return view('livewire.exchange-view',compact('exchange_rates'));
    }
}
