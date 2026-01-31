<?php

namespace App\Livewire\Promotion;

use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;
    public $titles = "", $oldImage = "";

    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate("required|file|image|max:5120")]
    public $image = '';

    #[Validate("required|string")]
    public $description = '';

    #[Validate("required|integer")]
    public $order = '';

    #[Validate("required|boolean")]
    public $isActive = true;

    #[On('createModal')]
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->title = "";
        $this->image = "";
        $this->oldImage = "";
        $this->description = "";
        $this->order = "";
        $this->isActive = true;
        $this->dispatch('open-modal', ['id' => 'promotion-modal']);
    }

    #[On('editModal')]
    public function openEditModal($id)
    {
        $promotion = \App\Models\Promotion::findOrFail($id);
        $this->resetValidation();
        $this->title = $promotion->title;
        $this->image = "";
        $this->oldImage = $promotion->image;
        $this->description = $promotion->description;
        $this->order = $promotion->order;
        $this->isActive = $promotion->is_active;
        $this->dispatch('open-modal', ['id' => 'promotion-modal']);
    }

    public function render()
    {
        return view('livewire.promotion.create')->layout('components.layouts.app', ['title' => $this->titles]);
    }
}
