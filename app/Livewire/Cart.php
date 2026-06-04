<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Address;
use App\Models\Cart as ModelsCart;
use App\Models\Coupon;
use App\Models\Outlet;
use Livewire\Component;
use App\Models\Shipping;
use App\Models\CouponUsage;
use App\Models\Transaction;
use App\Models\CouponProduct;
use App\Models\MinimumOrder;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Minimum;

use function Pest\Laravel\session;

class Cart extends Component
{
    public $carts, $qty, $subtotal, $coupon, $cpn = false, $c, $message, $addresses, $address, $outlet, $note, $outlets, $min, $shipping_date, $packaging_fee;
    public $fulfillment = 'delivery';

    public $isProcessing = false;

    public function mount()
    {

        if (Auth::user()->addresses->isEmpty()) {
            return redirect(route('settings.address'))->with('info', 'Please add your address before checkout');
        }

        $this->carts();

        if ($this->carts->count() == 0) {
            return redirect(route('b2b-home'));
        }

        $this->addresses = Auth::user()->addresses;
        $this->address = $this->addresses->first();

        $this->outlets = Outlet::where("is_active", true)->orderBy("name", "asc")->get();
        $this->outlet = $this->outlets->first();

        $this->setMinShippingDate();
        $this->shipping_date = $this->min;
    }

    public function checkout()
    {
        if ($this->isProcessing) {
            return;
        }
        $this->dispatch('modal-close', name: 'checkoutModal');

        $this->carts();

        // Acquire a cache lock to prevent concurrent checkout runs for the same user
        $lockKey = 'checkout_user_' . Auth::id();
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            Session::flash('error', 'Checkout is already in progress. Please wait a moment and try again.');
            return;
        }

        try {
            if (is_null(Auth::user()->Businesses) || Auth::user()->Businesses?->status != 'approved') {
                Session::flash('error', 'Your business is not approved yet. Please contact admin.');
                return;
            }
            // jika ada transaksi yang sudah melewati due_date (overdue) untuk user
            // yang memiliki tenor, blokir checkout sampai pelunasan.
            if ($this->checkPayment()) {
                $this->isProcessing = false;
                return;
            }

            // return;

            $this->setMinShippingDate();

            // Validasi shipping date: pastikan tidak kurang dari minimal tanggal pengiriman
            if (empty($this->shipping_date)) {
                Session::flash('error', 'Please choose a shipping date.');
                $this->isProcessing = false;
                return;
            }

            try {
                $minDate = Carbon::parse($this->min)->startOfDay();
                $shipDate = Carbon::parse($this->shipping_date)->startOfDay();
            } catch (\Throwable $e) {
                Session::flash('error', 'Invalid shipping date format.');
                $this->isProcessing = false;
                return;
            }

            if ($shipDate->lt($minDate)) {
                Session::flash('error', 'Shipping date must be on or after ' . $minDate->toDateString() . '. Please adjust shipping date.');
                $this->isProcessing = false;
                return;
            }

            if ($this->fulfillment === 'delivery') {
                // ensure address is selected
                if (empty($this->address) || is_null($this->address->id)) {
                    $this->isProcessing = false;
                    Session::flash('error', 'Please select a delivery address before checkout.');
                    return;
                }

                if (Auth::user()->Businesses->minimum_order > 0) {
                    if (Setting::where('key', 'use_tax_inclusive')->value('value') === 'true') {
                        $subtotal = $this->subtotal;
                    } else {
                        $subtotal = $this->subtotal + $this->packaging_fee;
                    }

                    if ($subtotal < Auth::user()->Businesses->minimum_order) {
                        Session::flash('error', 'Minimum order for your business is Rp. ' . number_format(Auth::user()->Businesses->minimum_order, 0, ',', '.'));
                        $this->isProcessing = false;
                        return;
                    }
                } else {
                    $minimumOrder = MinimumOrder::where('village_id', $this->address->village_id)->first();

                    if ($minimumOrder && $this->subtotal < $minimumOrder->minimum) {
                        Session::flash('error', 'Minimum order to ' . ($minimumOrder->village->name ?? '') . ' is Rp. ' . number_format($minimumOrder->minimum, 0, ',', '.'));
                        $this->isProcessing = false;
                        return;
                    }
                }
            }



            $this->isProcessing = true;

            try {
                DB::beginTransaction();
                $transaction = Transaction::create([
                    'subtotal' => $this->subtotal,
                    'transaction_number' => Transaction::transactionNumberGenerator(),
                    'discount' => $this->countDiscount(),
                    'total' => $this->subtotal + $this->packaging_fee - $this->countDiscount(),
                    'packaging_fee' => $this->packaging_fee,
                    'shipping_date' => $this->shipping_date,
                    'due_date' => Carbon::now()->addDays(Auth::user()->Businesses->tenor)->setTime(19, 0)->toDateTimeString(),
                    'user_id' => Auth::user()->id,
                    'status' => 'ordered',
                    'note' => $this->note ?? null,
                    'coupon_id' => $this->c->id ?? null,

                ]);

                if ($this->fulfillment === 'delivery') {
                    // At this point address should be present (guarded above), but double-check to avoid exceptions.
                    $shippingData = [
                        'user_id' => Auth::user()->id,
                        'transaction_id' => $transaction->id,
                        'name' => $this->address?->name ?? '',
                        'type' => $this->fulfillment,
                        'phone' => $this->address?->phone ?? '',
                        'email' => Auth::user()->email,
                        'address' => $this->address?->address ?? '',
                    ];
                } else {
                    // pickup: ensure outlet exists
                    if (empty($this->outlet) || is_null($this->outlet->id)) {
                        $this->isProcessing = false;
                        Session::flash('error', 'Please select an outlet before checkout.');
                        DB::rollBack();
                        return;
                    }

                    $shippingData = [
                        'user_id' => Auth::user()->id,
                        'transaction_id' => $transaction->id,
                        'type' => $this->fulfillment,
                        'name' => Auth::user()->Businesses?->name ?? Auth::user()->name ?? '',
                        'phone' => Auth::user()->phone,
                        'email' => Auth::user()->email,
                        'address' => $this->outlet?->name ?? '',
                    ];
                }

                $shipping = Shipping::create($shippingData);

                foreach ($this->carts as $key => $item) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $item->product_id,
                        'qty' => $item->qty,
                        'price' => $item->product->price,
                        'subtotal' => $item->qty * $item->product->price
                    ]);
                }

                if ($this->coupon ?? false) {
                    CouponUsage::create(['coupon_id' => $this->c->id, 'transaction_id' => $transaction->id]);
                }

                // $this->carts()->delete();
                ModelsCart::where('user_id', Auth::user()->id)->delete();

                DB::commit();
                Mail::to(Auth::user()->email)->queue(new \App\Mail\Order\Order($transaction->slug));
                $this->redirect(route('history'));
            } catch (\Throwable $th) {
                DB::rollBack();
                $this->dispatch('modal-close', name: 'checkoutModal');
                return;

                if (config('app.debug', false))
                    throw $th;
                Session::flash('error', $th->getMessage());
            }
        } finally {
            // ensure lock is always released so subsequent checkouts are allowed
            $lock->release();
        }
    }

    /**
     * Cek apakah user memiliki transaksi yang sudah lewat tenor (overdue).
     * Hanya berlaku untuk user yang memiliki bisnis dengan tenor > 0.
     *
     * @return bool true jika ada overdue (block checkout), false jika tidak
     */
    public function checkPayment()
    {
        $business = Auth::user()->Businesses;
        if (is_null($business) || ($business->tenor ?? 0) <= 0) {
            return false;
        }

        // Cari transaksi milik user yang belum 'paid' dan due_date sudah lewat
        $overdueExists = Transaction::where('user_id', Auth::user()->id)
            ->where(function ($q) {
                $q->where('status', '!=', 'paid')
                    ->orWhereNull('status');
            })
            ->whereNotNull('due_date')
            ->where('due_date', '<', Carbon::now())
            ->exists();

        if ($overdueExists) {
            Session::flash('error', 'You have overdue transactions. Please settle outstanding invoices before checkout.');
            // juga bisa dispatch event untuk UI
            $this->dispatch('error', ['message' => 'overdue']);
            return true;
        }

        return false;
    }

    public function pn($state)
    {
        $this->cpn = $state;
    }

    public function carts()
    {
        $this->carts = ModelsCart::where('user_id', Auth::user()->id)->get();
        $this->qty = $this->carts->toArray();

        $subtotal = 0;

        foreach ($this->carts as $key => $item) {
            $subtotal += $item->qty * $item->product->price;
        }
        if (Setting::where('key', 'use_tax_inclusive')->value('value') === 'true') {
            $this->subtotal = $subtotal * 100 / 103;
            $this->packaging_fee = $subtotal - $this->subtotal;
        } else {
            $this->subtotal = $subtotal;
            $this->packaging_fee = 0.03 * $subtotal;
            $this->packaging_fee = (int) $this->packaging_fee;
        }
        // dd($this->packaging_fee);

        $this->setMinShippingDate();
    }

    public function minus($id)
    {
        $cart = ModelsCart::where('id', $id)->first();
        if ($cart->qty <= $cart->product->moq) {
            $cart->update(['qty' => $cart->product->moq]);
            Session::flash('info', "{$cart->product->name} has MOQ on {$cart->product->moq} pcs");
        } else {
            $cart->decrement('qty');
        }
        $this->carts();
    }

    public function plus($id)
    {
        ModelsCart::where('id', $id)->first()->increment('qty');
        $this->carts();
    }

    public function change()
    {
        foreach ($this->qty as $key => $item) {
            $cart = ModelsCart::where('id', $item['id'])->first();
            if ($item['qty'] <= $cart->product->moq) {
                $cart->update(['qty' => $cart->product->moq]);
                Session::flash('info', "{$cart->product->name} has MOQ on {$cart->product->moq} pcs");
            } else {
                $cart->update(['qty' => $item['qty']]);
            }
        }
        $this->carts();
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            $cart = ModelsCart::where('id', $id)->first();

            $cart->delete();
            DB::commit();
            $this->carts();
        } catch (\Throwable $th) {
            DB::rollBack();
            if (config('app.debug') == true) {
                throw $th;
            } else {
                Session::flash('error', $th->getMessage());
            }
        }
    }

    public function updatedCoupon()
    {
        $coupon = Coupon::where('code', $this->coupon)->first();

        if ($coupon) {
            if ($coupon->is_active()) {
                $this->c = $coupon;
                $this->message = "Coupon Applied";
            } else {
                $this->c = '';
                $this->message = "Coupon Expired";
            }
        } else {
            $this->c = '';
            $this->message = "Coupon Invalid";
        }
    }

    public function countDiscount()
    {
        if (!$this->c ?? false) {
            return 0;
        }
        if ($this->subtotal > $this->c->minimum) {
            if ($this->c->type == 'fixed') {
                return $this->c->amount;
            } else {
                $discount = 0;
                foreach ($this->carts as $key => $item) {
                    $link = CouponProduct::where('coupon_id', $this->c->id)->where('product_id', $item->product->id)->first();

                    if ($link) {
                        $discount += $this->c->amount / 100 * $item->product->price * $item->qty;
                    }
                }
                if ($this->c->maximum > 0) {
                    $discount = min($discount, $this->c->maximum);
                }
                return $discount;
            }
        }
        return 0;
    }

    public function setMinShippingDate()
    {
        $now = Carbon::now();

        $min = 1;

        $add = 0;

        if (!$now->lt($now->copy()->setTime(17, 0))) {
            // sebelum jam 5 sore → minimal besok
            $min++;
        }

        // Jika ada produk yang qty-nya melebihi maximum_order dan cutoff_time
        // sudah lewat (atau tidak disetel), tambahkan 1 hari minimum pengiriman.
        foreach ($this->carts as $item) {
            $product = $item->product;
            $max = $product->maximum_order ?? 0;

            // jika maximum_order tidak disetel atau nol => tidak ada batas
            if (!$max || $max <= 0) {
                continue;
            }

            if ($item->qty <= $max) {
                // masih dalam batas
                continue;
            }

            // pada titik ini: ada produk dengan qty > maximum_order
            // Periksa cutoff_time: jika disetel dan sekarang masih sebelum cutoff,
            // maka maximum_order TIDAK berlaku (boleh dikirim besok).
            $cutoff = $product->cutoff_time ?? null;
            if ($cutoff) {
                try {
                    // parse cutoff time (mis. "15:00:00" atau "15:00")
                    $cutoffToday = Carbon::parse($cutoff);

                    // pastikan cutoffToday berada pada hari ini
                    $cutoffToday->year = $now->year;
                    $cutoffToday->month = $now->month;
                    $cutoffToday->day = $now->day;
                } catch (\Throwable $e) {
                    $cutoffToday = null;
                }

                // jika ada cutoff dan sekarang masih sebelum cutoff -> lewati (maximum tidak berlaku)
                if ($cutoffToday && $now->lt($cutoffToday)) {
                    continue;
                }
            }

            // tidak ada cutoff atau sekarang sudah melewati cutoff -> maximum berlaku
            $add = 1;
            break;
        }

        $this->min = $now->copy()->addDays($min + $add)->toDateString();
    }

    public function openShowModal($jurnal_id)
    {
        $this->dispatch('showModal', jurnal_id: $jurnal_id);
    }

    public function changeOutlet($id)
    {
        $this->outlet = Outlet::find($id);
        $this->dispatch('modal-close', name: 'outlet');
    }

    public function changeAddress($id)
    {
        $this->address = Address::find($id);

        $this->dispatch('modal-close', name: 'address');
    }

    public function render()
    {
        // Pada render halaman cart.blade.php, buatin untuk kirim data list dari outlet yang ada juga
        // Dan method buat change nya juga
        return view('livewire.cart')->layout('components.layouts.app.header', ['title' => 'My Cart']);
    }
}
