<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\SubCategory;
use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\CategoryRequest;
use App\Services\EsbApiRequest\SubCategoryRequest;
use Livewire\Component;

class CategoryIndex extends Component
{
    public $categories, $subCategories;

    public $title = "Categories / Sub Categories";

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
        $category->active = !$category->active;
        $category->save();
        $this->getCategory();
    }

    public function sync(EsbApiAuth $auth)
    {
        $category = $this->syncCategories($auth);
        $subCategory = $this->syncSubCategories($auth);

        if (!$category || !$subCategory) {
            return;
        }

        session()->flash('success', "Category and Subcategory success synced");
        $this->getCategory();
        $this->getSubCategories();
    }

    public function syncCategories(EsbApiAuth $auth)
    {
        $request = new CategoryRequest(new EsbApiRequest($auth));

        $filters = ['limit' => 9999];

        $response = $request->getCategories($filters);

        // dd($response);

        if (!$response || $response['status'] === "fail") {
            if (config('app.debug')) {
                throw new \Exception($response['message'] ?? '');
            }
            session()->flash('error', $response['message'] ?? '');
            return 0;
        }

        $result = $response['result']['data'];

        foreach ($result as $key => $item) {
            Category::updateOrCreate(
                ['categoryID' => $item['categoryID']],
                $item
            );
        }

        return 1;
    }

    public function syncSubCategories(EsbApiAuth $auth)
    {
        $request = new SubCategoryRequest(new EsbApiRequest($auth));

        $filters = ['limit' => 9999];

        $response = $request->getSubCategories($filters);

        if (!$response ||  $response['status'] === "fail") {
            if (config('app.debug')) {
                throw new \Exception($response['message'] ?? '');
            }
            session()->flash('error', $response['message'] ?? '');
            return 0;
        }

        $result = $response['result']['data'];

        foreach ($result as $key => $item) {

            SubCategory::updateOrCreate(
                ['subCategoryID' => $item['subCategoryID']],
                $item
            );
        }

        return 1;
    }

    public function render()
    {
        return view('livewire.category-index')->layout('components.layouts.app', ['title' => $this->title]);
    }
}