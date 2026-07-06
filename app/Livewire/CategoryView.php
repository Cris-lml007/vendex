<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoryView extends Component
{

    public function getCategory($id){
        $this->dispatch('getCategory',$id)->to(CategoryForm::class);
    }

    public function getBrand($id){
        $this->dispatch('getBrand',$id)->to(BrandForm::class);
    }


    #[On('refresh')]
    public function render()
    {
        $heads = ['Id', 'Nombre', 'Acciones'];
        $categories = Category::paginate();
        $brands = Brand::all();
        return view('livewire.category-view', compact(['heads','categories','brands']));
    }
}
