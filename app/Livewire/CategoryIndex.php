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
    public $categories;

    public $title = "Categories";

    public $open = 0;

    public function mount()
    {
        $this->getCategory();
    }

    public function getCategory()
    {
        $this->categories = Category::all();
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
    }

    public function syncCategories(EsbApiAuth $auth)
    {
        $request = new CategoryRequest(new EsbApiRequest($auth));

        $filters = ['limit' => 9999];

        $response = $request->getCategories($filters);

        if (!$response || $response['httpCode'] > 400 || $response['status'] === "fail") {
            if (config('app.debug')) {
                throw new \Exception($response['message'] ?? '');
            }
            session()->flash('error', $response['message'] ?? '');
            return 0;
        }

        $result = $response['result']['data'];

        Category::updateOrCreate(
            ['categoryID' => $result['categoryID']],
            $result
        );

        return 1;
    }
    public function syncSubCategories(EsbApiAuth $auth)
    {
        $request = new SubCategoryRequest(new EsbApiRequest($auth));

        $filters = ['limit' => 9999];

        $response = $request->getSubCategories($filters);

        if (!$response || $response['httpCode'] > 400 || $response['status'] === "fail") {
            if (config('app.debug')) {
                throw new \Exception($response['message'] ?? '');
            }
            session()->flash('error', $response['message'] ?? '');
            return 0;
        }

        $result = $response['result']['data'];

        SubCategory::updateOrCreate(
            ['subCategoryID' => $result['subCategoryID']],
            $result
        );

        return 1;
    }

    public function toggleOpen($id)
    {
        $this->open = $id;
    }

    public function render()
    {
        return view('livewire.category-index')->layout('components.layouts.app', ['title' => $this->title]);
    }
}
