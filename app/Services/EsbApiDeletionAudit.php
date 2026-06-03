<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class EsbApiDeletionAudit
{
    public function recordDeletion(
        int $deletedCount,
        bool $success = true,
        string $requestType = null,
        string $errorMessage = null,
        string $triggeredBy = 'scheduler'
    ): int {
        return DB::table('esb_api_deletion_audits')->insertGetId([
            'deleted_records_count' => $deletedCount,
            'request_type' => $requestType,
            'deleted_before' => now(),
            'deletion_criteria' => $requestType ? json_encode(['request_type' => $requestType]) : null,
            'error_message' => $errorMessage,
            'success' => $success,
            'triggered_by' => $triggeredBy,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function getAuditLog(array $filters = [])
    {
        $query = DB::table('esb_api_deletion_audits')->orderBy('created_at', 'desc');

        if (isset($filters['success'])) {
            $query->where('success', boolval($filters['success']));
        }

        if (isset($filters['triggered_by'])) {
            $query->where('triggered_by', $filters['triggered_by']);
        }

        if (isset($filters['request_type'])) {
            $query->where('request_type', $filters['request_type']);
        }

        if (isset($filters['created_after'])) {
            $query->where('created_at', '>=', $filters['created_after']);
        }

        if (isset($filters['created_before'])) {
            $query->where('created_at', '<=', $filters['created_before']);
        }

        return $query->get();
    }

    public function getLatestAudit()
    {
        return DB::table('esb_api_deletion_audits')
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function getTotalDeletedRecords(): int
    {
        return (int) DB::table('esb_api_deletion_audits')
            ->where('success', true)
            ->sum('deleted_records_count');
    }
}
