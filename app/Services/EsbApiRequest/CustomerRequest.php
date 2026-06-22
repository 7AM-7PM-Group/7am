<?php

namespace App\Services\EsbApiRequest;

use App\Services\EsbApiRequest as BaseEsbApiRequest;

class CustomerRequest
{
    protected BaseEsbApiRequest $request;

    public function __construct(BaseEsbApiRequest $request)
    {
        $this->request = $request;
    }

    public function getCustomers(array $filters = [])
    {
        $path = '/customer';

        if (! empty($filters)) {
            $path .= '?'.http_build_query($filters);
        }

        return $this->request->get($path);
    }

    public function getCustomer($id)
    {
        return $this->request->get("/customer/{$id}");
    }

    public function createCustomer(array $data)
    {
        return $this->request->post('/customer', 'core', $data);
    }

    public function updateCustomer($id, array $data)
    {
        return $this->request->put("/customer/{$id}", $data);
    }

    public function deleteCustomer($id)
    {
        return $this->request->delete("/customer/{$id}");
    }
}
