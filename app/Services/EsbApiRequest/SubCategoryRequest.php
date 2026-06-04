<?php

namespace App\Services\EsbApiRequest;

use App\Services\EsbApiRequest as BaseEsbApiRequest;

class SubCategoryRequest
{
    protected BaseEsbApiRequest $request;

    public function __construct(BaseEsbApiRequest $request)
    {
        $this->request = $request;
    }

    public function getSubCategories(array $filters = [])
    {
        $path = '/product/sub-category';

        if (!empty($filters)) {
            $path .= '?' . http_build_query($filters);
        }

        return $this->request->get($path);
    }

    public function getSubCategory($id)
    {
        return $this->request->get("/product/sub-category/{$id}");
    }
}
