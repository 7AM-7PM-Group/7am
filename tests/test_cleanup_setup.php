<?php

use App\Services\EsbApiRequestLog;
use App\Services\EsbApiDeletionAudit;

// Insert a test log entry with past expiration
DB::table('esb_api_request_logs')->insert([
    'method' => 'GET',
    'request_url' => 'https://stg7.esb.co.id/core-stg/v1/test',
    'request_body' => null,
    'user_id' => null,
    'request_source' => 'system',
    'request_type' => 'sync_success',
    'expired_at' => now()->subDays(5),
    'response_code' => 200,
    'response_body' => json_encode(['status' => 'ok']),
    'success' => true,
    'retried_with_refresh' => false,
    'error_message' => null,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✅ Test log inserted\n";

// Check expired records
$log = new EsbApiRequestLog();
$expired = $log->getExpiredRecords();
echo "📊 Found expired records: " . count($expired) . "\n";

// Verify audit table is empty
$audit = new EsbApiDeletionAudit();
$auditCount = DB::table('esb_api_deletion_audits')->count();
echo "📋 Audit entries: " . $auditCount . "\n";

dd('Test setup complete');
