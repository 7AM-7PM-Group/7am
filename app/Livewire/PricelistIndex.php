<?php

namespace App\Livewire;

use App\Models\Pricelist;
use Livewire\Attributes\On;
use Livewire\Component;

class PricelistIndex extends Component
{

    public $title = "Pricelist";

    public $pricelists = [], $id, $pricelist;

    public function mount()
    {
        $this->getPricelists();
    }

    #[On('refreshPricelist')]
    public function getPricelists()
    {
        $this->pricelists = Pricelist::all();
    }

    public function showDeleteModal($id)
    {
        $this->id = $id;
        $this->pricelist = Pricelist::find($id);
        if (!$this->pricelist) {
            session()->flash('error', 'Pricelist not found.');
            return;
        }
        $this->dispatch('modal-show', name: 'delete-pricelist-modal');
    }

    public function deletePricelist($id)
    {
        try {
            $this->id = $id;
            Pricelist::find($id)->delete();
            $this->dispatch('modal-close', name: 'delete-pricelist-modal');
            $this->getPricelists();
            session()->flash('success', 'Pricelist deleted successfully');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function createPricelist()
    {
        $this->dispatch('createPricelist');
    }

    public function editPricelist($id)
    {
        $this->id = $id;
        $this->dispatch('editPricelist', id: $id);
    }

    public function render()
    {
        return view('livewire.pricelist-index')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
