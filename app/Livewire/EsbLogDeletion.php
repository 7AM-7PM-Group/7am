<?php

namespace App\Livewire;

use Livewire\Component;

class EsbLogDeletion extends Component
{

    public $title = "";

    public function render()
    {
        return view('livewire.esb-log-deletion')->layout('components.layouts.app', ['title'=>$this->title]);
    }
}
