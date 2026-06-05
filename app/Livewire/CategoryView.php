<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoryView extends Component
{

    public function getCategory($id){
        $this->dispatch('getCategory',$id)->to(CategoryForm::class);
    }


    #[On('refresh')]
    public function render()
    {
        $heads = ['Id', 'Nombre', 'Acciones'];
        $categories = Category::paginate();
        return view('livewire.category-view', compact(['heads','categories']));
    }
}
