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

    public function getTransactions(array $filters = [], array $logContext = [])
    {
        $path = '/api/user';

        if (!empty($filters)) {
            $path .= '?' . http_build_query($filters);
        }

        return $this->request->get($path, $logContext);
    }

    public function getTransaction($id, array $logContext = [])
    {
        return $this->request->get("/v1/transactions/{$id}", $logContext);
    }

    public function createTransaction(array $data, array $logContext = [])
    {
        return $this->request->post('/v1/transactions', $data, $logContext);
    }

    public function updateTransaction($id, array $data, array $logContext = [])
    {
        return $this->request->put("/v1/transactions/{$id}", $data, $logContext);
    }

    public function deleteTransaction($id, array $logContext = [])
    {
        return $this->request->delete("/v1/transactions/{$id}", $logContext);
    }
}
