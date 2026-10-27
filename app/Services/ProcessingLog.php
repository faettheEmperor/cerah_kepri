<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProcessingLog
{
    public static function start(string $process, ?string $scope = null, array $metadata = []): int
    {
        $now = now();

        return (int) DB::table('processing_logs')->insertGetId([
            'process' => $process,
            'scope' => $scope,
            'started_at' => $now,
            'status' => 'running',
            'metadata' => $metadata === [] ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function finish(int $id, int $processed, int $succeeded, int $failed, int $written, array $metadata = []): void
    {
        DB::table('processing_logs')->where('id', $id)->update([
            'finished_at' => now(),
            'status' => $failed === 0 ? 'succeeded' : ($succeeded > 0 ? 'partial' : 'failed'),
            'records_processed' => max(0, $processed),
            'records_succeeded' => max(0, $succeeded),
            'records_failed' => max(0, $failed),
            'records_written' => max(0, $written),
            'metadata' => $metadata === [] ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'updated_at' => now(),
        ]);
    }

    public static function fail(int $id, \Throwable $exception, int $processed = 0, int $written = 0, array $metadata = []): void
    {
        DB::table('processing_logs')->where('id', $id)->update([
            'finished_at' => now(),
            'status' => 'failed',
            'records_processed' => max(0, $processed),
            'records_succeeded' => 0,
            'records_failed' => max(1, $processed),
            'records_written' => max(0, $written),
            'error_message' => mb_substr($exception->getMessage(), 0, 60000),
            'metadata' => $metadata === [] ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'updated_at' => now(),
        ]);
    }
}
