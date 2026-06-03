<?php

namespace App\Services\EsbApiRequest;

use App\Services\EsbApiRequest as BaseEsbApiRequest;

class CategoryRequest
{
    protected BaseEsbApiRequest $request;

    public function __construct(BaseEsbApiRequest $request)
    {
        $this->request = $request;
    }

    public function getCategories(array $filters = [])
    {
        $path = '/v1/categories';

        if (!empty($filters)) {
            $path .= '?' . http_build_query($filters);
        }

        return $this->request->get($path);
    }

    public function getCategory($id)
    {
        return $this->request->get("/v1/categories/{$id}");
    }
}
