<?php

namespace App\Livewire;

use Exception;
use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Payment;
use App\Models\PaymentInvoice;
use Livewire\Component;
use App\Models\Shipping;
use App\Models\Transaction;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Checkout extends Component
{

    public $transaction, $snapToken;

    public function mount($slug)
    {

        // if (is_null(Auth::user()->Businesses) || Auth::user()->Businesses?->status != 'approved') {
        //     return redirect(route('b2b-home'))->with('error', "Please Verified Business First!");
        // }

        try {
            $this->transaction = Transaction::where('slug', $slug)->first();



            if (!$this->transaction) {
                throw new Exception("Sorry, Your order not found!");
            } else if ($this->transaction->status != 'ordered') {
                throw new Exception("Sorry, You have paid this order");
            }

            Config::$serverKey = config('midtrans.serverKey');
            Config::$isProduction = config('midtrans.isProduction');
            Config::$isSanitized = config('midtrans.isSanitized');
            Config::$is3ds = config('midtrans.is3ds');


            if (!$this->transaction->hasActivePaymentLink()) {
                $order_id = $this->transaction->generateOrderId();
                $midtransParams = [
                    'transaction_details' => [
                        'order_id' => $order_id,
                        'gross_amount' => $this->transaction->total,
                    ],
                    'customer_details' => [
                        'first_name' => $this->transaction->shipping->name,
                        'phone' => $this->transaction->shipping->phone,
                        'email' => $this->transaction->shipping->email,
                        // 'address' => $this->transaction->shipping->address,
                    ],
                ];
                $this->snapToken = Snap::getSnapToken($midtransParams);

                $paymentInvoice = PaymentInvoice::create([
                    'transaction_id' => $this->transaction->id,
                    'midtrans_order_id' => $order_id,
                    'expired_at' => Auth::user()->Businesses->tenor == 0 ? now()->setTime(19, 0) : now()->addDay(),
                    'snap_token' => $this->snapToken,
                ]);
            } else {
                $this->snapToken = $this->transaction->activePayment()->snap_token;
            }

            // dd(env('MIDTRANS_TARGET_LINK'));

            // dd($this->snapToken);
        } catch (\Throwable $th) {
            if (config('app.debug') == true) {
                throw $th;
            } else {
                return redirect(route('history'))->with('error', $th->getMessage());
            }
        }
    }

    #[On('paymentSuccess')]
    public function paymentSuccess($result)
    {
        // $arrya = [
        //     "status_code" => "200",
        //     "status_message" => "Success, transaction is found",
        //     "transaction_id" => "f1a30dab-c5dc-4c51-b9ef-919915d8c5e8",
        //     "order_id" => "TRX202511050003",
        //     "gross_amount" => "1.00",
        //     "payment_type" => "qris",
        //     "transaction_time" => "2025-11-05 12:49:29",
        //     "transaction_status" => "settlement",
        //     "fraud_status" => "accept",
        //     "finish_redirect_url" => "http://example.com?order_id=TRX202511050003&status_code=200&transaction_status=settlement",
        // ];

        // dd($result['order_id']);

        try {
            DB::beginTransaction();
            $invoice = PaymentInvoice::where('midtrans_order_id', $result['order_id'])->firstOrFail();

            $transaction = $invoice->transaction;

            // dd($transaction);

            $transaction->update(['status' => "paid"]);

            Payment::create([
                'transaction_id' => $transaction->id,
                'amount' => $result['gross_amount'],
                'payment_type' => $result['payment_type'],
                'payment_status' => 'paid',
            ]);
            DB::commit();
            Mail::to(Auth::user()->email)->queue(new \App\Mail\Order\Paid($transaction->slug));

            return redirect(route('invoice', ['slug' => $transaction->slug]));
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug') == true) {
                throw $th;
            } else {
                session()->flash('error', $th->getMessage());
            }
        }
    }

    public function cancelOrder($slug)
    {
        $transaction = Transaction::where('slug', $slug)->first();

        if (!$transaction) {
            session()->flash('error', "Transaction not found");
        }

        if ($transaction->status != 'ordered' || $transaction->mekari_sync_status != 'pending' || now()->diffInMinutes($transaction->created_at) >= 5) {
            session()->flash('error', "Your order cannot be cancelled");
            return;
        }

        $transaction->delete();
        return redirect(route('history'))->with('success', "Order has been cancelled");
    }

    public function render()
    {
        return view('livewire.checkout')->layout('components.layouts.app.header', ['title' => 'Checkout']);
    }
}
