<?php

namespace App\Livewire\Promotion;

use Livewire\Component;

class Index extends Component
{
    public $title = "Our Promotions";
    public $promotions = [];

    public function mount()
    {
        $this->promotions = \App\Models\Promotion::orderBy('order', 'asc')->get();
    }

    public function getPromotions()
    {
        $this->promotions = \App\Models\Promotion::orderBy('order', 'asc')->get();
    }

    public function openCreateModal()
    {
        dd('asdfasd');
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
        return view('livewire.promotion.index')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
