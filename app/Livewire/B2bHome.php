<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\SetCategory;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class B2bHome extends Component
{
    public $products;

    public $categories;

    public function mount()
    {
        $user = Auth::user();
        // Jika user login, punya relasi Businesses, dan relasi setCategory pada salah satu Businesses
        if ($user && $user->Businesses && $user->Businesses->setCategory) {
            // Jika user punya bisnis dan bisnis itu punya setCategory
            $setCategory = $user->Businesses->setCategory->id;
        } else {
            // Jika tidak, gunakan set category default dari setting
            $setCategory = Setting::where('key', 'default_set_category')->value('value');
            // dd(false, $this->categories, $defaultSetCategory);
        }
        // dd($setCategory);
        $this->products = Product::latest()->filters(['set_category' => $setCategory])->active()->take(12)->get();
    }

    public function openShowModal($productID)
    {
        $this->dispatch('showModal', productID: $productID);
    }

    public function render()
    {
        return view('livewire.b2b-home')->layout('components.layouts.app.header', ['title' => 'Home']);
    }
}
