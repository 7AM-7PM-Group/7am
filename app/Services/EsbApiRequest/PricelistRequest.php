<?php

namespace App\Services\EsbApiRequest;

use App\Services\EsbApiRequest as BaseEsbApiRequest;

class PricelistRequest
{
    protected BaseEsbApiRequest $request;

    public $baseUrl = '/customer-pricelist';

    public function __construct(BaseEsbApiRequest $request)
    {
        $this->request = $request;
    }

    public function getCustomerPricelists(array $filters = [])
    {
        $path = $this->baseUrl;


        if (!empty($filters)) {
            $path .= '?' . http_build_query($filters);
        }

        return $this->request->get($path);
    }

    public function getCustomerPricelist($id)
    {
        return $this->request->get($this->baseUrl . "/{$id}");
    }

    public function createCustomerPricelist(array $data)
    {
        return $this->request->post($this->baseUrl, $data);
    }

    public function updateCustomerPricelist($id, array $data)
    {
        return $this->request->put($this->baseUrl . "/{$id}", $data);
    }

    public function deleteCustomerPricelist($id)
    {
        return $this->request->delete($this->baseUrl . "/{$id}");
    }
}
