<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class History extends Component
{
    public $transactions, $oldTransaction;

    public function mount()
    {
        $this->getHistory();

        $this->getOldHistory();

        // dd($this->transactions);
    }

    public function getHistory()
    {
        $this->transactions = Transaction::where('user_id', Auth::user()->id)->latest('created_at')->get();
    }

    public function getOldHistory() {
        $this->oldTransaction = DB::connection('mysql2')->table('transactions')->where('user_id', Auth::user()->id)->latest('created_at')->get() ?? collect();
    }

    public function cancelOrder($slug)
    {
        $transaction = Transaction::where('slug', $slug)->first();

        if (!$transaction) {
            session()->flash('error', "Transaction not found");
        }

        if ($transaction->status != 'ordered' || $transaction->mekari_sync_status != 'pending' || now()->diffInMinutes($transaction->created_at) >= 5) {
            session()->flash('error', "Your order cannot be cancelled");
        } else {
            $transaction->delete();

            $this->getHistory();

            session()->flash('success', "Order has been cancelled");
        }
    }

    public function render()
    {
        return view('livewire.history')->layout('components.layouts.app.header', ['title' => 'History Transaction']);
    }
}
