<?php

namespace App\Http\Livewire;

use Livewire\Component;

class HelloWorld extends Component
{
    public $name = 'Uttu';
    
    public function render()
    {
        return view('livewire.hello-world');
    }
}
