<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Product;
use App\Models\SetCategory;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ShopIndex extends Component
{
    use WithPagination;

    public $filter = false;

    public $categories;

    #[Url(except: '')]
    public $search = '';

    public $min = '';

    public $max = '';

    #[Url(except: '')]
    public $category = '';

    public function mount()
    {
        $user = Auth::user();

        // Jika user login, punya relasi Businesses, dan relasi setCategory pada salah satu Businesses
        if ($user && $user->Businesses && $user->Businesses->setCategory) {
            // Jika user punya bisnis dan bisnis itu punya setCategory
            $this->categories = $user->Businesses?->setCategory?->categories ?? collect();

            // dd(true, $this->categories);
        } else {
            // Jika tidak, gunakan set category default dari setting
            // $defaultSetCategoryId = Setting::where('key', 'default_set_category')->value('value');
            // $defaultSetCategory = SetCategory::find($defaultSetCategoryId);

            $this->categories = Category::all() ?? collect();
            // dd(false, $this->categories, $defaultSetCategory);
        }

        // dd(Auth::user()?->Businesses?->setCategory->id);
    }

    public function resetFilter()
    {
        // $this->category = '';
        $this->search = '';
        $this->min = '';
        $this->max = '';
        $this->category = '';
    }

    public function openShowModal($productID)
    {
        $this->dispatch('showModal', productID: $productID);
    }

    public function toogleFilter()
    {
        $this->filter = ! $this->filter;
    }

    public function render()
    {
        // Ambil set_category terbaru langsung dari DB (menghindari relasi Auth yang stale)
        $setCategoryId = null;
        // if (Auth::check()) {
        //     $setCategoryId = \App\Models\Business::where('user_id', Auth::id())
        //         ->whereNotNull('set_category_id')
        //         ->value('set_category_id');
        // }

        // if (!$setCategoryId) {
        //     $setCategoryId = Setting::where('key', 'default_set_category')->value('value');
        // }
        // Perbarui daftar kategori berdasarkan set_category yang aktif
        // $this->categories = SetCategory::find($setCategoryId)?->categories ?? collect();

        $products = Product::filters([
            'search' => $this->search,
            'category' => $this->category,
            'min' => $this->min,
            'max' => $this->max,
            'set_category' => $setCategoryId,
        ])->paginate(24)->withQueryString();

        // dd([
        //     'auth' => Auth::id(),
        //     'business_set_category' => \App\Models\Business::where('user_id', Auth::id())->first(),
        //     'final_set_category_id' => $setCategoryId,
        // ]);

        return view('livewire.shop-index', compact('products'))->layout('components.layouts.app.header', ['title' => 'Shop']);
    }
}
