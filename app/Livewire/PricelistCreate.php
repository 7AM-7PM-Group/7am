<?php

namespace App\Livewire;

use App\Models\Pricelist;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PricelistCreate extends Component
{

    public $title = "", $id;

    #[Validate("required|string")]
    public $name = '';

    #[Validate("required")]
    public $PricelistID = '';

    #[On("createPricelist")]
    public function createPricelist()
    {
        $this->resetValidation();
        $this->title = "Create Pricelist";
        $this->name = '';
        $this->PricelistID = '';
        $this->dispatch('modal-show', name: 'create-pricelist-modal');
    }

    #[On("editPricelist")]
    public function editPricelist($id)
    {
        $pricelist = Pricelist::find($id);
        if (!$pricelist) {
            session()->flash('error', 'Pricelist not found.');
            return;
        }
        $this->resetValidation();
        $this->id = $id;
        $this->name = $pricelist->name;
        $this->PricelistID = $pricelist->PricelistID;
        $this->title = "Edit Pricelist";
        $this->dispatch('modal-show', name: 'create-pricelist-modal');
    }

    public function save()
    {
        $validated = $this->validate();
        try {
            Pricelist::updateOrCreate(['id' => $this->id], $validated);
            $this->dispatch('modal-close', name: 'create-pricelist-modal');
            session()->flash('success', 'Pricelist saved successfully');
            $this->dispatch('refreshPricelist');
        } catch (\Throwable $th) {
            if (config('app.debug', false)) throw $th;
            session()->flash('error', $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pricelist-create')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
