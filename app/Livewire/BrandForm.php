<?php

namespace App\Livewire;

use App\Models\Brand;
use Livewire\Attributes\On;
use Livewire\Component;

class BrandForm extends Component
{
    public $name;
    public $made;
    public $color_text = "black";
    public $color_bg = "white";
    public Brand $brand;


    public function mount(Brand $brand = null){
        if($brand->id != null){
            $this->brand = $brand;
            $this->name = $brand->name;
            $this->made = $brand->made;
            $this->color_bg = $brand->color_bg;
            $this->color_text = $brand->color_text;
        }else{
            $this->brand = new Brand();
        }

    }

    #[On('getBrand')]
    public function get($id){
        $this->brand = Brand::find($id);
        if($this->brand?->id != null){
            $this->name = $this->brand->name;
            $this->made = $this->brand->made;
            $this->color_text = $this->brand->color_fg;
            $this->color_bg = $this->brand->color_bg;
        }else{
            $this->brand = new Brand();
        }
    }

    public function save(){
        $this->validate([
            'name' => 'required',
            'made' => 'required',
        ], attributes: ['made' => 'Origen']);

        if($this->brand?->id == null){
            $this->brand = new Brand();
        }
        $this->brand->name = $this->name;
        $this->brand->made = $this->made;
        $this->brand->color_fg = $this->color_text;
        $this->brand->color_bg = $this->color_bg;
        $this->brand->save();

        $this->restart();

        $this->js('$("#modal-brand").modal("hide")');
        $this->dispatch('refresh')->to(CategoryView::class);
    }

    public function restart()
    {
        $this->brand = new Brand();
        $this->name = null;
        $this->made = null;
        $this->color_bg = null;
        $this->color_text = null;

    }

    public function render()
    {
        return view('livewire.brand-form');
    }
}
