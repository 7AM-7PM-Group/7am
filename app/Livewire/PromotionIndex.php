<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class PromotionIndex extends Component
{
    public $title = "Our Promotions";
    public $promotions = [];

    public function mount()
    {
        $this->promotions = \App\Models\Promotion::orderBy('order', 'asc')->get();

        $this->openEditModal(1);
    }

    #[On('refreshPromotionList')]
    public function getPromotions()
    {
        $this->promotions = \App\Models\Promotion::orderBy('order', 'asc')->get();
    }

    public function openCreateModal()
    {
        // dd('asdfasd');
        $this->dispatch('createModal');
    }

    public function openEditModal($id)
    {
        $this->dispatch('editModal', [$id]);
    }

    public function delete($id)
    {
        $promotion = \App\Models\Promotion::findOrFail($id);
        $promotion->delete();
    }

    public function render()
    {
        return view('livewire.promotion-index')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
