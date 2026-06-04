<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\SetCategory;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BusinessIndex extends Component
{
    public $title = "All Registered Business", $business, $businesses,  $setCategory;

    #[Validate('required')]
    public $status = '';

    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $sts = '';

    #[Validate('required_if:status,accepted')]
    public $name = '', $set_category_id;

    public function mount()
    {
        $this->getBusiness();
        // $this->request(1, 'rejected');
        $this->setCategory = SetCategory::all();
    }

    public function openDetailModal($id)
    {
        $this->business = Business::find($id);
        $this->dispatch('modal-show', name: 'detail-business');
    }

    public function updatedSearch()
    {
        $this->getBusiness();
    }

    public function resetFilter()
    {
        $this->search = '';
        $this->sts = '';
        $this->getBusiness();
    }

    public function updatedSts()
    {
        $this->getBusiness();
    }

    public function getBusiness()
    {
        $this->businesses = Business::filters(['search' => $this->search, 'status' => $this->sts])->get();
    }

    public function render()
    {
        // $bisnis = $this->businesses->paginate(24);
        // dd($bisnis->user);
        return view('livewire.business-index')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
