<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Business;
use App\Models\SetCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;

class BusinessModal extends Component
{
    public $title = "All Registered Business", $business, $setCategory = [];
    public $id = null;
    protected $paginationTheme = 'tailwind';

    #[Validate('required')]
    public $status = '';

    #[Validate('required_if:status,approved')]
    public $name = '', $set_category_id = '', $tenor = 0, $minimum_order = 0;

    protected $listeners = [
        'openCreateBusinessModal' => 'openCreateBusinessModal',
        'openEditBusinessModal' => 'openEditBusinessModal'
    ];

    public function rules()
    {
        return [
            'name' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'set_category_id' => 'nullable|exists:set_categories,id',
            'tenor' => 'nullable|integer',
        ];
    }

    private function resetForm()
    {
        $this->resetValidation();
        $this->reset(['id', 'business', 'setCategory', 'status', 'name', 'set_category_id', 'tenor']);
    }

    // public function openCreateBusinessModal()
    // {
    //     $this->resetForm();
    //     $this->dispatch('modal-show', name: 'business-modal');
    // }

    public function openEditBusinessModal($id)
    {
        $this->resetValidation();
        $this->business = Business::find($id);
        $this->setCategory = SetCategory::all();
        $this->id = $this->business->id;
        $this->name = $this->business->name;
        $this->status = $this->business->status == 'requested' ? 'approved' : $this->business->status;
        // cast to int so the bound value type matches the option values
        $this->set_category_id = $this->business->set_category_id !== null ? (int) $this->business->set_category_id : '';
        $this->tenor = $this->business->tenor ?? 0;
        $this->minimum_order = $this->business->minimum_order ?? 0;

        // force a component refresh so the select picks up the loaded options
        $this->dispatch('modal-show', name: 'business-modal');
        // dd($this->business);
        // dd($this->set_category_id);
    }

    public function save()
    {
        $business = $this->business;
        $validated = $this->validate();

        // dd($validated);

        try {
            DB::beginTransaction();
            $business->update($validated);
            DB::commit();
            $message = match ($this->status) {
                'approved' => 'Business berhasil diterima!',
                'rejected' => 'Business ditolak!',
                default => 'Business berhasil diupdate!'
            };
            session()->flash('success', $message);

            if ($this->status == 'approved') {
                Mail::to($business->user->email)->queue(new \App\Mail\Business\Accept($business->id));
            } elseif ($this->status == 'rejected') {
                Mail::to($business->user->email)->queue(new \App\Mail\Business\Decline($business->id));
            }

            $this->resetForm();
            $this->dispatch('modal-close', name: 'business-modal');
            $this->dispatch('refreshBusinessList');
            // dd($business);
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug') == true) {
                throw $th;
            } else {
                session()->flash('error', $th->getMessage());
            }
        }
    }

    public function render()
    {
        return view('livewire.business-modal');
    }
}
