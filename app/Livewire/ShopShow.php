<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ShopShow extends Component
{
    public $product;

    public $products = [];

    public $qty = 1;

    protected $jurnalApi;

    #[On('showModal')]
    public function openShowModal($productID)
    {
        $this->product = Product::where('productID', $productID)->firstOrFail();

        $this->qty = $this->product->moq;

        $this->dispatch('modal-show', name: 'shop-show');
    }

    public function addToCart()
    {

        $user = Auth::user();

        if (! $user) {
            return redirect(route('login'));
        }

        try {
            $cart = Cart::where('user_id', Auth::user()->id)->where('product_id', $this->product->id)->firstOrFail();

            $cart->increment('qty', $this->qty);
            $this->dispatch('updated');
        } catch (\Throwable $th) {
            Cart::create([
                'user_id' => Auth::user()->id,
                'product_id' => $this->product->id,
                'qty' => $this->qty,
            ]);
            $this->dispatch('created');
        }

        $this->qty = $this->product->moq;
    }

    public function render()
    {
        return view('livewire.shop-show')->layout('components.layouts.app', ['title' => 'asd']);
    }
}
