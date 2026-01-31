<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Promotion as PromotionModel;

class Promotion extends Component
{

    public $title = "Promotions";

    public $promotions = [];

    public function mount()
    {
        $this->promotions = PromotionModel::where('is_active', true)->orderBy('order', 'asc')->get();
    }

    public function render()
    {
        return view('livewire.promotion')->layout('components.layouts.promotion', ['title' => $this->title]);
    }
}
