<?php

namespace App\Livewire;

use App\Enums\Role;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryView extends Component
{

    use WithPagination;
    public $list1 = [
        'search' => '',
        'sort_field' => 'id',
        'sort_direction' => 'asc',
        'pages' => 1
    ];

    public $list2 = [
        'search' => '',
        'sort_field' => 'name',
        'sort_direction' => 'asc',
        'pages' => 1
    ];

    public function getCategory($id){
        $this->dispatch('getCategory',$id)->to(CategoryForm::class);
    }

    public function getBrand($id){
        $this->dispatch('getBrand',$id)->to(BrandForm::class);
    }

    public function updatedList1()
    {
        if($this->list1['pages'] != ''){
            $this->setPage($this->list1['pages'], pageName: 'categories-page');
        }
    }

    public function updatedList2()
    {
        if($this->list2['pages'] != ''){
            $this->setPage($this->list2['pages'], pageName: 'brands-page');
        }
    }

    public function categories(){
    }

    public function removeCategory($password,$id)
    {
        if(Hash::check($password, Auth::user()->password) && Auth::user()->role == Role::ADMIN){
            Category::destroy($id);
            return true;
        }else{
            return false;
        }
    }

    public function removeBrand($password,$id)
    {
        if(Hash::check($password, Auth::user()->password) && Auth::user()->role == Role::ADMIN){
            Brand::destroy($id);
            return true;
        }else{
            return false;
        }

    }

    #[On('refresh')]
    public function render()
    {
        $heads_category = [
            'Id' => 'id',
            'Nombre' => 'name',
            'Acciones' => null
        ];

        $heads_brand = [
            'Id' => 'id',
            'Nombre' => 'name',
            'Origen' => 'made',
            'Acciones' => null
        ];

        if($this->list1['search'] != ''){
            $categories = Category::where('name','like','%'.$this->list1['search'].'%')
                ->orWhere('id','like','%'.$this->list1['search'].'%')
                ->orderBy($this->list1['sort_field'],$this->list1['sort_direction'])
                ->paginate(15, pageName: 'categories-page');
        }else{
            $categories = Category::orderBy($this->list1['sort_field'],$this->list1['sort_direction'])
                ->paginate(15, pageName: 'categories-page');
        }

        //$categories = Category::paginate();

        if($this->list2['search'] != ''){
            $brands = Brand::where('name','like','%'.$this->list2['search'].'%')
                ->orWhere('id','like','%'.$this->list2['search'].'%')
                ->orWhere('made','like','%'.$this->list2['search'].'%')
                ->orderBy($this->list2['sort_field'],$this->list2['sort_direction'])
                ->paginate(15, pageName: 'brands-page');
        }else{
            $brands = Brand::orderBy($this->list2['sort_field'],$this->list2['sort_direction'])
                ->paginate(15, pageName: 'brands-page');
        }
        $this->list1['pages_max'] = $categories->lastPage();
        $this->list2['pages_max'] = $brands->lastPage();
        //$brands = Brand::all();
        return view('livewire.category-view', compact(['heads_category','heads_brand','categories','brands']));
    }
}
