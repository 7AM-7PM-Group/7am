<?php

namespace App\Http\Controllers;

use App\Services\EsbApiAuth;
use App\Services\EsbApiRequest;
use App\Services\EsbApiRequest\TransactionRequest;
use Illuminate\Http\Request;

class ApiTestController extends Controller
{
    private TransactionRequest $transactionRequest;

    public function __construct(EsbApiAuth $auth)
    {
        $request = new EsbApiRequest($auth);
        $this->transactionRequest = new TransactionRequest($request);
        // dd($auth, $request, $this->transactionRequest);
    }

    public function index(Request $request)
    {
        try {
            $filters = $request->only(['page', 'per_page', 'status', 'start_date', 'end_date']);
            $transactions = $this->transactionRequest->getTransactions($filters, $this->logContext($request, 'transaction_index'));

            return response()->json($transactions);
        } catch (\Exception $exception) {
            // dd($exception->getMessage());
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $payload = $request->all();
            $response = $this->transactionRequest->createTransaction($payload, $this->logContext($request, 'transaction_create'));

            return response()->json($response, 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $response = $this->transactionRequest->getTransaction($id, $this->logContext(request(), 'transaction_show'));

            return response()->json($response);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $payload = $request->all();
            $response = $this->transactionRequest->updateTransaction($id, $payload, $this->logContext($request, 'transaction_update'));

            return response()->json($response);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $response = $this->transactionRequest->deleteTransaction($id, $this->logContext(request(), 'transaction_delete'));

            return response()->json($response);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    private function logContext(Request $request, string $requestType): array
    {
        return [
            'user_id' => $request->user()?->id,
            'request_source' => 'user',
            'request_type' => $requestType,
        ];
    }
}
