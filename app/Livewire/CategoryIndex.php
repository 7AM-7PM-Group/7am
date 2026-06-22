<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\SubCategory;
use App\Services\EsbApiAuth;
use Livewire\Component;

class CategoryIndex extends Component
{
    public $categories;

    public $subCategories;

    public $title = 'Categories / Sub Categories';

    public function mount()
    {
        $this->getCategory();
        $this->getSubCategories();
    }

    public function getCategory()
    {
        $this->categories = Category::all();
    }

    public function getSubCategories()
    {
        $this->subCategories = SubCategory::all();
    }

    public function toggleStatus($id)
    {
        $category = Category::find($id);
        $category->active = ! $category->active;
        $category->save();
        $this->getCategory();
    }

    public function sync(EsbApiAuth $auth)
    {
        $category = Category::syncCategory($auth);
        $subCategory = SubCategory::syncSubCategory($auth);

        if (! $category || ! $subCategory) {
            return;
        }

        session()->flash('success', 'Category and Subcategory success synced');
        $this->getCategory();
        $this->getSubCategories();
    }

    public function render()
    {
        return view('livewire.category-index')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
