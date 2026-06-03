<?php

namespace App\Services\EsbApiRequest;

use App\Services\EsbApiRequest as BaseEsbApiRequest;

class ProductRequest
{
    protected BaseEsbApiRequest $request;

    public function __construct(BaseEsbApiRequest $request)
    {
        $this->request = $request;
    }

    public function getProducts(array $filters = [])
    {
        $path = '/v1/products';

        if (!empty($filters)) {
            $path .= '?' . http_build_query($filters);
        }

        return $this->request->get($path);
    }

    public function getProduct($id)
    {
        return $this->request->get("/v1/products/{$id}");
    }
}
