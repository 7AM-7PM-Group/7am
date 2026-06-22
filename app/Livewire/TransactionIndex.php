<?php

namespace App\Livewire;

use App\Mail\Order\Cancel;
use App\Models\Setting;
use App\Models\Transaction;
use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\TransactionRequest;
use App\Services\JurnalApi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TransactionIndex extends Component
{
    #[Url(except: '')]
    public $date = '';

    #[Url(except: '')]
    public $search = '';

    #[Url(except: '')]
    public $page = '';

    #[Url(except: '')]
    public $status = '';

    #[Validate('required')]
    public $cancellation_reason = '';

    public $transaction;

    public $transaction_number = '';

    public function mount()
    {
        $this->date = $this->date ?? date('Y-m-d');

        $this->transaction = collect(['transaction_number' => '6']);
    }

    public function updateDate()
    {
        // dd($this->date);
    }

    public function resetFilters()
    {
        $this->date = '';
        $this->search = '';
        $this->status = '';
        $this->page = '';
    }

    public function importSalesOrderToESB($id)
    {
        try {
            DB::beginTransaction();
            $transaction = Transaction::where('id', $id)->first();
            $business = $transaction->user->businesses;

            $request = new TransactionRequest(new EsbApiRequest(app(EsbApiAuth::class)));

            // dd($transaction);

            if (! $transaction) {
                Session::flash('error', 'Transaction not found');

                return;
            }

            if ($transaction->mekari_sync_status != 'pending') {
                Session::flash('error', "Transaction status are $transaction->mekari_sync_status");

                return;
            }

            $memo = $transaction->note;

            if ($transaction->shipping->type == 'delivery') {
                $memo .= "\nDelivery to {$transaction->shipping->name} - {$transaction->shipping->address}";
            } else {
                $memo .= "\nPick Up at {$transaction->shipping->name}";
            }

            $body = [
                'branchID' => 185,
                'productSalesDate' => Carbon::now()->format('Y-m-d'),
                'requiredDate' => Carbon::now()->addDays()->format('Y-m-d'),
                'currencyID' => 1,
                'rate' => 1,
                'customerID' => $business->customerID,
                'salesRepID' => null,
                'customerBranchID' => null,
                'linkPurchaseNum' => null,
                'customerAddress' => $transaction->shipping->address,
                'additionalInfo' => $transaction->shipping->type,

            ];

            foreach ($transaction->items as $key => $item) {
                $body['productSalesDetails'][] = [
                    'productDetailID' => $item->product->productID,
                    'qty' => $item->qty,
                    'priceListPrice' => 0,
                    'price' => $item->price,
                    'discount' => 0,
                    'discountPercent' => 0,
                    'vatValue' => 0,
                    'dppValue' => 0,
                    'notes' => null,
                ];
            }

            $response = $request->createTransaction($body);

            if (! isset($response['result'])) {
                throw new \Exception(($response['error_full_messages'][0] ?? 'Unknown error'));
            }

            $number = $response['result']['productSalesNum'];

            $transaction->update(['number' => $number, 'mekari_sync_status' => 'synced']);
            DB::commit();
            // dd($response1, $response);
            session()->flash('success', "Invoice $number imported successfully.");
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', "Transaction $transaction->transaction_number import failed: ".$th->getMessage());
            // if (config('app.debug', false)) throw $th;
        }
    }

    public function showCancelOrderModal($id)
    {
        $transaction = Transaction::where('id', $id)->first();

        if (! $transaction) {
            session()->flash('error', 'Transaction not found');

            return;
        }

        $this->transaction = $transaction;
        $this->transaction_number = $transaction->transaction_number;
        $this->cancellation_reason = '';
        $this->dispatch('modal-show', name: 'cancel-order');
    }

    public function cancelOrder()
    {
        $transaction = $this->transaction;

        if (! $transaction) {
            session()->flash('error', 'Transaction not found');
        }

        if ($transaction->status != 'ordered' || $transaction->mekari_sync_status != 'pending') {
            session()->flash('error', 'Your order cannot be cancelled');
        }
        try {
            DB::beginTransaction();
            $transaction->update([
                'cancellation_reason' => $this->cancellation_reason,
            ]);

            $transaction->delete();

            // $this->getHistory();

            DB::commit();
            $this->dispatch('modal-close', name: 'cancel-order');
            Mail::to($transaction->user->email)->queue(new Cancel($transaction->slug));

            session()->flash('success', 'Order has been cancelled');
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug', false)) {
                throw $th;
            }
            session()->flash('error', $th->getMessage());
        }
    }

    public function importPayment(JurnalApi $jurnalApi, $id)
    {
        try {
            DB::beginTransaction();
            $transaction = Transaction::where('id', $id)->first();
            if (! $transaction) {
                Session::flash('error', 'Transaction not found');

                return;
            }
            if ($transaction->payment->mekari_sync_status != 'pending') {
                Session::flash('error', "Payment status are {$transaction->payment->mekari_sync_status}");

                return;
            }
            $body = [
                'receive_payment' => [
                    'transaction_date' => $transaction->shipping_date->format('Y-m-d'),
                    'records_attributes' => [
                        [
                            'transaction_no' => $transaction->number,
                            'amount' => $transaction->total,
                        ],
                    ],
                    'custom_id' => 'ReceivePayment'.$transaction->number,
                    'payment_method_name' => Setting::where('key', 'payment_method_name')->value('value'),
                    'payment_method_id' => (int) Setting::where('key', 'payment_method_id')->value('value'),
                    'is_draft' => false,
                    'deposit_to_name' => Setting::where('key', 'payment_deposit_to_name')->value('value'),
                    'memo' => "Payment order $transaction->number",
                    'witholding_account_name' => Setting::where('key', 'payment_witholding_account_name')->value('value'),
                    'witholding_value' => (int) Setting::where('key', 'payment_witholding_value')->value('value'),
                    'witholding_type' => Setting::where('key', 'payment_witholding_type')->value('value'),
                ],
            ];

            $response1 = $jurnalApi->request(
                'POST',
                '/public/jurnal/api/v1/receive_payments',
                $body
            );

            $transaction->payment->update(['mekari_sync_status' => 'synced']);

            DB::commit();

            Session::flash('success', "Payment for transaction $transaction->number imported successfully.");
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug', false)) {
                throw $th;
            }
            session()->flash('error', $th->getMessage());
        }
    }

    #[On('update-transaction')]
    public function updateTransaction()
    {
        $this->render();
    }

    public function payTransaction($id)
    {
        // dd($id);
        $this->dispatch('pay-modal', id: $id);
    }

    public function render()
    {
        $transactions = Transaction::latest()
            ->filters([
                'date' => $this->date,
                'status' => $this->status,
                'search' => $this->search,
            ])
            ->paginate(24)->withQueryString();

        return view('livewire.transaction-index', compact('transactions'))->layout('components.layouts.app', ['title' => 'All Product']);
    }
}
