<?php

use Livewire\Component;

new class extends Component
{

    public $heads;

    public function mount($heads){
        $this->heads = $heads;
    }
};
