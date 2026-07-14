<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoryForm extends Component
{
    public $name;

    public Category $category;

    public function mount(Category $category = null){
        if($category != null){
            $this->category = $category;
            $this->name = $this->category->name;
        }else{
            $this->category = new Category();
        }

    }

    #[On('getCategory')]
    public function getCategory($id){
        $this->category = Category::find($id);
        $this->name = $this->category->name;
    }

    public function save(){
        $this->validate([
            'name' => 'required',
        ]);
        if($this->category?->id == null){
            $this->category = new Category();
        }
        $this->category->name = $this->name;
        $this->category->save();
        $this->reset();

        $this->category = new Category();
        $this->js('$("#modal-category").modal("hide")');
        $this->dispatch('refresh')->to(CategoryView::class);
    }


    public function render()
    {
        return view('livewire.category-form');
    }
}
