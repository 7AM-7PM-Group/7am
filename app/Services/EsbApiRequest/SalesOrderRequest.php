<?php

namespace App\Services\EsbApiRequest;

use App\Services\EsbApiRequest as BaseEsbApiRequest;

class SalesOrderRequest
{
    protected BaseEsbApiRequest $request;

    public function __construct(BaseEsbApiRequest $request)
    {
        $this->request = $request;
    }

    public function getSalesOrders(array $filters = [], array $logContext = [])
    {
        $path = '/sales/product-sales';

        if (!empty($filters)) {
            $path .= '?' . http_build_query($filters);
        }

        return $this->request->get($path, $logContext);
    }

    public function getSalesOrder($id, array $logContext = [])
    {
        return $this->request->get("/sales/product-sales/{$id}", $logContext);
    }

    public function createSalesOrder(array $data, array $logContext = [])
    {
        return $this->request->post('/sales/product-sales', $data, $logContext);
    }

    public function updateSalesOrder($id, array $data, array $logContext = [])
    {
        return $this->request->put("/sales/product-sales/{$id}", $data, $logContext);
    }

    public function deleteSalesOrder($id, array $logContext = [])
    {
        return $this->request->delete("/sales/product-sales/{$id}", $logContext);
    }
}