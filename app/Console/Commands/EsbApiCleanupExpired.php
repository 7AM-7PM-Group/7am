<?php

namespace App\Console\Commands;

use App\Services\EsbApiRequestLog;
use App\Services\EsbApiDeletionAudit;
use Illuminate\Console\Command;

class EsbApiCleanupExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'esb-api:cleanup-expired
                            {--request-type= : Filter by request type (sync_success, sync_failed, manual_cleanup)}
                            {--limit=1000 : Maximum records to delete per execution}
                            {--dry-run : Preview records to be deleted without deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired ESB API request logs based on retention policies';

    public function handle()
    {
        $this->info('🗑️  ESB API Request Log Cleanup Starting...');
        $this->newLine();

        try {
            $requestType = $this->option('request-type');
            $limit = (int) $this->option('limit');
            $dryRun = $this->option('dry-run');

            $logService = new EsbApiRequestLog();
            $auditService = new EsbApiDeletionAudit();

            if ($dryRun) {
                $this->handleDryRun($logService, $requestType, $limit);
                return 0;
            }

            // Get expired records count before deletion
            $expiredRecords = $logService->getExpiredRecords($limit);
            $recordsToDelete = $expiredRecords->count();

            if ($recordsToDelete === 0) {
                $this->info('✅ No expired records found for deletion.');
                $auditService->recordDeletion(
                    0,
                    true,
                    $requestType,
                    null,
                    'scheduler'
                );
                return 0;
            }

            $this->warn("📊 Found {$recordsToDelete} expired record(s) to delete");

            if (!$this->confirm('Do you want to proceed with deletion?', true)) {
                $this->info('❌ Deletion cancelled.');
                return 1;
            }

            // Perform deletion
            $deletedCount = $logService->deleteExpiredRecords($requestType, $limit);

            // Record audit
            $auditService->recordDeletion(
                $deletedCount,
                true,
                $requestType,
                null,
                'scheduler'
            );

            $this->newLine();
            $this->info("✅ Successfully deleted {$deletedCount} expired request log(s)");
            $this->line("📋 Audit recorded");

            return 0;
        } catch (\Throwable $exception) {
            $this->newLine();
            $this->error('❌ Cleanup failed: ' . $exception->getMessage());

            $auditService = new EsbApiDeletionAudit();
            $auditService->recordDeletion(
                0,
                false,
                $this->option('request-type'),
                $exception->getMessage(),
                'scheduler'
            );

            return 1;
        }
    }

    private function handleDryRun(EsbApiRequestLog $logService, ?string $requestType, int $limit): void
    {
        $expiredRecords = $logService->getExpiredRecords($limit);
        $count = $expiredRecords->count();

        if ($count === 0) {
            $this->info('✅ No expired records found.');
            return;
        }

        $this->warn("🔍 DRY RUN: Would delete {$count} record(s)");
        $this->newLine();
        $this->table(
            ['ID', 'Method', 'Request Type', 'Expired At', 'Success'],
            $expiredRecords->map(fn($record) => [
                $record->id,
                $record->method,
                $record->request_type,
                $record->expired_at,
                $record->success ? '✓' : '✗',
            ])->toArray()
        );
    }
}
