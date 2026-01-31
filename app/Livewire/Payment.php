<?php

namespace App\Livewire;

use App\Models\Transaction;
use Exception;
use Livewire\Component;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;
use SnapBi\SnapBi;

class Payment extends Component
{
    public $transaction, $snapToken;

    public function mount($slug)
    {
        return redirect()->route('history');
        try {

            $this->transaction = Transaction::where('slug', $slug)->first();

            if (!$this->transaction) {
                throw new Exception("Sorry, Your order not found!");
            }

            // Config::$serverKey = config('midtrans.serverKey');
            // Config::$isProduction = config('midtrans.isProduction');
            // Config::$isSanitized = config('midtrans.isSanitized');
            // Config::$is3ds = config('midtrans.is3ds');


            // if (!$this->transaction->snap_token) {

            //     $midtransParams = [
            //         'transaction_details' => [
            //             'order_id' => $this->transaction->number,
            //             'gross_amount' => $this->transaction->total,
            //         ],
            //         'customer_details' => [
            //             'first_name' => $this->transaction->shipping->name,
            //             'phone' => $this->transaction->shipping->phone,
            //             'email' => $this->transaction->shipping->email,
            //             // 'address' => $this->transaction->shipping->address,
            //         ],
            //     ];
            //     $this->snapToken = Snap::getSnapToken($midtransParams);

            //     $this->transaction->snap_token = $this->snapToken;
            //     $this->transaction->save();
            // } else {
            //     $this->snapToken = $this->transaction->snap_token;
            // }

            // dd($this->snapToken);
        } catch (\Throwable $th) {
            throw $th;
            return redirect(route('history'))->with('error', $th->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.payment')->layout('components.layouts.invoice', ['title' => "Pay your order"]);
    }
}
