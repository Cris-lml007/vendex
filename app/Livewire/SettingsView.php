<?php

namespace App\Livewire;

use App\Models\Settings;
use Livewire\Component;

class SettingsView extends Component
{
    public $section = null;

    public $wholesale_price = false;

    public $serialized_products = false;
    public $inherited_products = false;

    public $transfers_to_all = false;

    public $change_password = false;

    public $show_tutorial = false;

    public $theme = 'vendex';

    public $version = 1;

    public Settings $settings;

    public function mount(){
        $settings = Settings::first();
        if($settings?->id == null){
            Settings::create([
                'wholesale_price' => false,
                'serialized_products' => false,
                'heredaded_products' => false,
                'transfers_all' => false,
                'change_password' => false,
                'tutorial' => true,
                'theme' => 'vendex'
            ]);
        }else{
            $this->wholesale_price = $settings->wholesale_price;
            $this->serialized_products = $settings->serialized_products;
            $this->inherited_products = $settings->heredaded_products;
            $this->transfers_to_all = $settings->transfers_all;
            $this->change_password = $settings->change_password;
            $this->show_tutorial = $settings->tutorial;
            $this->theme = $settings->theme;
        }
        $this->settings = $settings;
    }

    public function updatedWholesalePrice(){
        $this->settings->wholesale_price = $this->wholesale_price;
        $this->settings->save();
    }

    public function updatedSerializedProducts(){
        $this->settings->serialized_products = $this->serialized_products;
        $this->settings->save();
    }
    public function updatedInheritedProducts(){
        $this->settings->heredaded_products = $this->inherited_products;
        $this->settings->save();
    }
    public function updatedTransfersToAll(){
        $this->settings->transfers_all = $this->transfers_to_all;
        $this->settings->save();
    }
    public function updatedChangePassword(){
        $this->settings->change_password = $this->change_password;
        $this->settings->save();
    }
    public function updatedShowTutorial(){
        $this->settings->tutorial = $this->show_tutorial;
        $this->settings->save();
    }


    public function setTheme(string $name){
        $this->theme = $name;
        $this->js("document.body.dataset.theme='{$name}'");
    }

    public function saveTheme(){
        $this->settings->theme = $this->theme;
        $this->settings->save();
        $this->js('Swal.fire({title: "Tema Guardado",icon:"success",showConfirmButton: false,timer:1500})');
    }



    public function render()
    {
        return view('livewire.settings-view');
    }
}
