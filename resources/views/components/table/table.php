<?php

use Livewire\Component;
use Livewire\Attributes\Modelable;

new class extends Component
{
    #[Modelable]
    public $list = [];
    public $search;
    public $sort_field;
    public $sort_direction = 'asc';


    public $heads;

    public function mount($heads){
        $this->heads = $heads;

        $this->sort_field = $this->list['sort_field'];
        $this->sort_direction = $this->list['sort_direction'];
        $this->search = $this->list['search'];
    }

    public function updatedSearch(){
        $this->list['search'] = $this->search;
    }

    public function sortBy($field)
    {
        if ($this->sort_field == $field) {
            $this->sort_direction = $this->sort_direction == 'asc' ? 'desc' : 'asc';
            $this->list['sort_direction'] = $this->sort_direction;
        } else {
            $this->sort_field = $field;
            $this->sort_direction = 'asc';
            $this->list['sort_field'] = $field;
            $this->list['sort_direction'] = 'asc';
        }
    }

};
