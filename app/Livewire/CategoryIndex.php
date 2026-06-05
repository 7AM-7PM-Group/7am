<?php

namespace App\Livewire;

use App\Models\Category;
use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\CategoryRequest;
use App\Services\JurnalApi;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CategoryIndex extends Component
{
    public $categories;
    protected $request;

    public function mount(EsbApiAuth $auth)
    {
        $this->request = new CategoryRequest(new EsbApiRequest($auth));

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

    public function sync(JurnalApi $jurnalApi)
    {
        Category::sync($jurnalApi);
        $this->getCategory();
    }


    public function render()
    {
        return view('livewire.category-index')->layout('components.layouts.app', ['title' => "Categories"]);
    }
}
