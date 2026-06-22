<?php

namespace App\Livewire;

use App\Models\Business;
use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\CustomerRequest;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BusinessSyncFromEsb extends Component
{
    #[Validate('required')]
    public $customerID = '';

    public $name = '';

    public $id;

    public $business;

    public $customerList = [];

    public $title = '';

    public function mount()
    {
        $this->title = 'Sync Customer from ESB';
    }

    protected function customerRequest(): CustomerRequest
    {
        return new CustomerRequest(new EsbApiRequest(app(EsbApiAuth::class)));
    }

    #[On('syncCustomerFromESB')]
    public function openSyncModal($id)
    {
        $this->business = Business::find($id);
        $this->id = $id;
        $this->name = $this->business?->name ?? '';

        $filters = ['limit' => 9999];
        $response = $this->customerRequest()->getCustomers($filters);

        if (! $response) {
            if (config('app.debug')) {
                throw new \Exception('Failed to fetch customers from ESB.');
            }
            session()->flash('error', 'Failed to fetch customers from ESB.');

            return;
        }

        if ($response['status'] === 'ok') {
            $this->customerList = collect($response['result']['data'])->pluck('customerName', 'customerID')->toArray();
        } else {
            if (config('app.debug')) {
                throw new \Exception('Failed to fetch customers from ESB: '.($response['message'] ?? 'Unknown error'));
            }
            session()->flash('error', 'Failed to fetch customers from ESB.');

            return;
        }

        $this->dispatch('modal-show', name: 'sync-customer-from-esb-modal');
    }

    public function save()
    {
        if ($this->business->customerID) {
            session()->flash('error', 'Customer Sudah Sync dengan dengan ESB.');

            return;
        }

        // dd($this->customerID);

        logger('customer isnt synced from ESB');

        $response = $this->customerRequest()->getCustomer($this->customerID);

        if (! $response) {
            return;
        }
        logger('get customer data from ESB');

        if ($response['status'] !== 'ok') {
            if (config('app.debug')) {
                throw new \Exception('Failed to sync customer from ESB: '.($response['message'] ?? 'Unknown error'));
            }
            session()->flash('error', 'Failed to sync customer.');

            return;
        }

        logger('get customer data from ESB');

        $this->business->update(
            [
                'customerID' => $response['result']['customerID'],
                'name' => $response['result']['customerName'],
            ]
        );

        // session()->flash('success', 'Customer synced successfully!');
        $this->dispatch('modal-close', name: 'sync-customer-from-esb-modal');
        $this->dispatch('successSyncFromEsb');
    }

    public function render()
    {
        return view('livewire.business-sync-from-esb')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
