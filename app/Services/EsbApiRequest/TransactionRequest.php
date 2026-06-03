<?php

namespace App\Services\EsbApiRequest;

use App\Services\EsbApiRequest as BaseEsbApiRequest;

class TransactionRequest
{
    protected BaseEsbApiRequest $request;

    public function __construct(BaseEsbApiRequest $request)
    {
        $this->request = $request;
    }

    public function getTransactions(array $filters = [])
    {
        $path = '/v1/transactions';

        if (!empty($filters)) {
            $path .= '?' . http_build_query($filters);
        }

        return $this->request->get($path);
    }

    public function getTransaction($id)
    {
        return $this->request->get("/v1/transactions/{$id}");
    }

    public function createTransaction(array $data)
    {
        return $this->request->post('/v1/transactions', $data);
    }

    public function updateTransaction($id, array $data)
    {
        return $this->request->put("/v1/transactions/{$id}", $data);
    }

    public function deleteTransaction($id)
    {
        return $this->request->delete("/v1/transactions/{$id}");
    }
}
