<?php

namespace App\Livewire;

use App\Models\Business;
use App\Models\SetCategory;
use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\CustomerRequest;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
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

    #[On('refreshBusinessList')]
    public function getBusiness()
    {
        $this->businesses = Business::filters(['search' => $this->search, 'status' => $this->sts])->get();
    }

    public function importCustomerToESB(EsbApiAuth $auth, $id)
    {
        $request = new CustomerRequest(new EsbApiRequest($auth));

        $business = Business::find($id);

        $customerData = [
            "customerName" => $business->name,
            "customerCode" => null,
            "customerCategoryID" => 1, // need adjustment
            "receivableCoaNo" => null,
            "paymentID" => 1, // 1 cash, 2 credit
            "paymentDueDays" => $business->tenor,
            "country" => "Indonesia",
            "state" => "Indonesia",
            "city" => null,
            "district" => null,
            "subDistrict" => null,
            "zipCode" => null,
            "address" => $business->address,
            "block" => null,
            "number" => null,
            "rt" => null,
            "rw" => null,
            "phone1" => null,
            "phone2" => null,
            "fax" => null,
            "salesRepID" => null,
            "notes" => null,
            "vatSubject" => 0, // need adjustment
            "lockVAT" => 0, // need adjustment
            "npwp" => $business->npwp,
            "customerPic" => [
                [
                    "greetingID" => 1, // 1 Mr, 2 Mrs, 3 Ms
                    "picName" => $business->representative,
                    "email" => $business->user->email,
                    "cellPhone" => $business->phone,
                    "flagDefault" => 1,
                    "flagSendingSOEmail" => 1
                ]
            ],
            "customerBank" => [
                [
                    "bankName" => $business->bank,
                    "bankAccountNumber" => $business->account_number,
                    "bankAccountName" => $business->account_name
                ]
            ],
            "customerBranch" => [
                [
                    "branchName" => null,
                ]
            ],
            "customerTax" => [
                "countryTax" => null,
                "stateTax" => null,
                "cityTax" => null,
                "districtTax" => null,
                "subDistrictTax" => null,
                "zipCodeTax" => null,
                "addressTax" => null,
                "blockTax" => null,
                "numberTax" => null,
                "rtTax" => null,
                "rwTax" => null,
                "phoneTax" => null,
                "flagReferAddress" => 0
            ],
        ];

        $response = $request->createCustomer($customerData);

        if (!$response) {
            return;
        }

        if ($response['status'] === "ok") {
            $business->update(['esbCustomerId' => $response['result']['customerID']]);
            session()->flash('success', 'Customer imported successfully!');
        } else {
            session()->flash('error', 'Failed to import customer.');
        }
    }

    public function syncCustomerFromESB($id)
    {
        // dd($id);
        $this->dispatch('syncCustomerFromESB', id: $id);
    }

    public function render()
    {
        // $bisnis = $this->businesses->paginate(24);
        // dd($bisnis->user);
        return view('livewire.business-index')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
