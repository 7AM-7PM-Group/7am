<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class EsbApiRequestLog
{
    private const RETENTION_DAYS = [
        'sync_success' => 30,
        'sync_failed' => 15,
        'manual_cleanup' => 7,
    ];

    public function store(array $data)
    {
        $requestType = $data['request_type'] ?? null;
        if (!$requestType) {
            $requestType = ($data['success'] ?? false) ? 'sync_success' : 'sync_failed';
        }

        return DB::table('esb_api_request_logs')->insertGetId([
            'method' => $data['method'] ?? null,
            'request_url' => $data['request_url'] ?? null,
            'request_body' => $data['request_body'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'request_source' => $data['request_source'] ?? 'system',
            'request_type' => $requestType,
            'expired_at' => now()->addDays($this->getRetentionDays($requestType)),
            'response_code' => $data['response_code'] ?? null,
            'response_body' => $data['response_body'] ?? null,
            'success' => $data['success'] ?? false,
            'retried_with_refresh' => $data['retried_with_refresh'] ?? false,
            'error_message' => $data['error_message'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function getById($id)
    {
        return DB::table('esb_api_request_logs')
            ->where('id', $id)
            ->first();
    }

    public function getAll(array $filters = [])
    {
        $query = DB::table('esb_api_request_logs')->orderBy('created_at', 'desc');

        if (isset($filters['method'])) {
            $query->where('method', $filters['method']);
        }

        if (isset($filters['success'])) {
            $query->where('success', boolval($filters['success']));
        }

        if (isset($filters['retried_with_refresh'])) {
            $query->where('retried_with_refresh', boolval($filters['retried_with_refresh']));
        }

        if (isset($filters['request_type'])) {
            $query->where('request_type', $filters['request_type']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['request_source'])) {
            $query->where('request_source', $filters['request_source']);
        }

        if (isset($filters['created_after'])) {
            $query->where('created_at', '>=', $filters['created_after']);
        }

        if (isset($filters['created_before'])) {
            $query->where('created_at', '<=', $filters['created_before']);
        }

        return $query->get();
    }

    public function getExpiredRecords(int $limit = 1000)
    {
        return DB::table('esb_api_request_logs')
            ->where('expired_at', '<=', now())
            ->limit($limit)
            ->get();
    }

    public function deleteExpiredRecords(string $requestType = null, int $limit = 1000): int
    {
        $query = DB::table('esb_api_request_logs')
            ->where('expired_at', '<=', now());

        if ($requestType) {
            $query->where('request_type', $requestType);
        }

        return $query->limit($limit)->delete();
    }

    public function getRetentionDays(string $requestType): int
    {
        return self::RETENTION_DAYS[$requestType] ?? self::RETENTION_DAYS['sync_success'];
    }

    public function deleteAllRecords(): int
    {
        return DB::table('esb_api_request_logs')->delete();
    }
}
