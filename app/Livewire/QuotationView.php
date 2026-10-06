<?php

namespace App\Livewire;

use App\Models\Quotation;
use Livewire\Component;

class QuotationView extends Component
{

    public $list = [
        'sort_field' => 'id',
        'sort_direction' => 'desc',
        'search' => ''
    ];


    public function getQuotation($id){
        return redirect(route('admin.quotation.id',$id));
    }

    public function render()
    {
        $heads = [
            'ID' => 'id',
            'Valido desde' => 'valid_from',
            'Valido Hasta' => 'valid_to',
            'Creado Por' => 'name'
        ];

        $data = Quotation::paginate();

        return view('livewire.quotation-view',compact(['heads','data']));
    }
}
