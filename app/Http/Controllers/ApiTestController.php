<?php

namespace App\Http\Controllers;

use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\TransactionRequest;
use Illuminate\Http\Request;

class ApiTestController extends Controller
{
    private TransactionRequest $transactionRequest;

    public function __construct()
    {
        $auth = new EsbApiAuth(
            config('ESB.esb_username'),
            config('ESB.esb_password'),
            config('ESB.esb_base_url'),
            config('ESB.esb_environment', 'sandbox')
        );

        $request = new EsbApiRequest($auth);
        $this->transactionRequest = new TransactionRequest($request);
    }

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['page', 'per_page', 'status', 'start_date', 'end_date']);
            $transactions = $this->transactionRequest->getTransactions($filters);

            return response()->json($transactions);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $payload = $request->all();
            $response = $this->transactionRequest->createTransaction($payload);

            return response()->json($response, 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $response = $this->transactionRequest->getTransaction($id);

            return response()->json($response);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $payload = $request->all();
            $response = $this->transactionRequest->updateTransaction($id, $payload);

            return response()->json($response);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $response = $this->transactionRequest->deleteTransaction($id);

            return response()->json($response);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
