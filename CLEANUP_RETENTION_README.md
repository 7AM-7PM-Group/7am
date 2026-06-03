# ESB API Retention Cleanup - Implementation Summary

## Overview
Implemented automated retention cleanup system for ESB API request logs with scheduler integration, audit trail, and interactive command interface.

## ✅ Implemented Components

### 1. Database Migrations

#### `database/migrations/2026_06_03_000001_create_esb_api_request_logs_table.php`
- **Fields Added**: `request_type` (varchar 30), `expired_at` (timestamp)
- **Indices**: `expired_at`, `request_type`
- **Status**: ✅ Migrated

#### `database/migrations/2026_06_04_000001_create_esb_api_deletion_audits_table.php`
- **Purpose**: Track all deletion operations
- **Fields**: id, deleted_records_count, request_type, deleted_before, deletion_criteria (JSON), error_message, success, triggered_by, timestamps
- **Status**: ✅ Migrated

### 2. Services

#### `app/Services/EsbApiRequestLog.php`
**Retention Policy Constants:**
```php
private const RETENTION_DAYS = [
    'sync_success' => 30,      // Successful API calls
    'sync_failed' => 15,       // Failed API calls
    'manual_cleanup' => 7,     // Forced cleanups
];
```

**Key Methods:**
- `store(array $data)` - Insert log entry with auto-calculated expiry
- `getExpiredRecords(int $limit = 1000)` - Find records past expiration
- `deleteExpiredRecords(string $requestType = null, int $limit = 1000): int` - Batch delete
- `getRetentionDays(string $requestType): int` - Lookup retention policy
- `deleteAllRecords(): int` - Nuclear option

#### `app/Services/EsbApiDeletionAudit.php` (NEW)
**Purpose**: Record and query deletion audit logs

**Key Methods:**
- `recordDeletion(int $deletedCount, bool $success, ...)` - Create audit entry
- `getAuditLog(array $filters = [])` - Query with filtering (success, triggered_by, request_type, date range)
- `getLatestAudit()` - Get most recent cleanup
- `getTotalDeletedRecords(): int` - Sum of all deleted records

### 3. Console Command

#### `app/Console/Commands/EsbApiCleanupExpired.php` (NEW)

**Signature:**
```bash
esb-api:cleanup-expired [--request-type=...] [--limit=1000] [--dry-run]
```

**Options:**
- `--request-type` - Filter by type (sync_success, sync_failed, manual_cleanup)
- `--limit` - Maximum records to delete per execution (default: 1000)
- `--dry-run` - Preview without deleting

**Features:**
- ✅ Dry-run mode with table preview
- ✅ Interactive confirmation before deletion
- ✅ Automatic audit recording
- ✅ Success/error logging to console and app logs
- ✅ Error handling with audit trail

### 4. Scheduler Integration

#### `bootstrap/app.php`
```php
->withSchedule(function (Schedule $schedule) {
    $schedule->command('esb-api:cleanup-expired')->dailyAt('02:00');
})
```

**Cron Expression:** `0 2 * * *` (Daily at 02:00 UTC)

**Status**: ✅ Verified via `php artisan schedule:list`

## Usage Examples

### Manual Cleanup
```bash
# Preview what will be deleted
php artisan esb-api:cleanup-expired --dry-run

# Delete all expired records (interactive confirmation)
php artisan esb-api:cleanup-expired

# Delete only sync_failed records without confirmation
php artisan esb-api:cleanup-expired --request-type=sync_failed -n

# Delete up to 500 records at a time
php artisan esb-api:cleanup-expired --limit=500
```

### Schedule Verification
```bash
# List all scheduled tasks
php artisan schedule:list

# Test the cleanup command immediately
php artisan schedule:test esb-api:cleanup-expired

# Run all due scheduled commands
php artisan schedule:run
```

### Audit Querying (via Tinker or Controllers)
```php
use App\Services\EsbApiDeletionAudit;
use Carbon\Carbon;

$audit = new EsbApiDeletionAudit();

// Get last 30 days of cleanups
$logs = $audit->getAuditLog([
    'created_after' => Carbon::now()->subDays(30)
]);

// Get failed cleanups
$failures = $audit->getAuditLog(['success' => false]);

// Get total deleted
$total = $audit->getTotalDeletedRecords();
```

## Testing Checklist

- [x] Migrations created and applied
- [x] Command help displays correctly
- [x] Dry-run mode works (found 0 expired records as expected)
- [x] Scheduler registered (cron: `0 2 * * *`)
- [x] Command auto-discovery confirmed
- [x] Audit table structure verified
- [x] Services instantiate without errors

## API Integration Points

### Log Entry Creation
When `EsbApiRequest->request()` completes, it calls:
```php
$this->log->store([
    'method' => $method,
    'request_url' => $url,
    'request_body' => $body,
    'user_id' => $logContext['user_id'] ?? null,
    'request_source' => $logContext['request_source'] ?? 'system',
    'response_code' => $responseData['httpCode'],
    'response_body' => json_encode($responseData['result']),
    'success' => $responseData['httpCode'] < 400,
    'retried_with_refresh' => $retried,
    'error_message' => $errorMessage,
    // request_type and expired_at auto-calculated
]);
```

### Cleanup Execution
- **Automatic**: Daily at 02:00 UTC via scheduler
- **Manual**: Run command anytime
- **Audit**: Every cleanup recorded in `esb_api_deletion_audits`

## Architecture Notes

1. **Nonstandard Bootstrap**: App uses `bootstrap/app.php` configuration instead of `Console/Kernel.php`
2. **Scheduler Integration**: Defined via `withSchedule()` callback in bootstrap
3. **Command Discovery**: Auto-scanned from `app/Console/Commands/`
4. **Import Fix**: Must use `Illuminate\Console\Scheduling\Schedule` class, not Facade
5. **Batch Processing**: Configurable limit to avoid large transactions

## Future Enhancements

- [ ] Admin dashboard for cleanup history visualization
- [ ] Configurable retention policies per environment
- [ ] Slack/email notifications for failed cleanups
- [ ] API endpoint for manual cleanup triggering
- [ ] Retention policy override per request type
- [ ] Archival to storage instead of deletion
- [ ] Performance metrics for cleanup duration

## Files Modified/Created

**Created:**
- `database/migrations/2026_06_04_000001_create_esb_api_deletion_audits_table.php`
- `app/Services/EsbApiDeletionAudit.php`
- `app/Console/Commands/EsbApiCleanupExpired.php`

**Modified:**
- `database/migrations/2026_06_03_000001_create_esb_api_request_logs_table.php` (added request_type, expired_at)
- `app/Services/EsbApiRequestLog.php` (added retention constants and cleanup methods)
- `bootstrap/app.php` (added withSchedule callback)

## Support Commands

```bash
# View all ESB logs created in last 7 days
php artisan tinker
>>> DB::table('esb_api_request_logs')->where('created_at', '>=', now()->subDays(7))->count()

# Check audit trail
>>> DB::table('esb_api_deletion_audits')->latest()->get()

# View expired records (without deleting)
>>> (new \App\Services\EsbApiRequestLog())->getExpiredRecords(10)

# Database inspection
php artisan db

# View schedule in cron format
php artisan schedule:list
```
