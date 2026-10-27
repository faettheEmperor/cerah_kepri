<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ShowProcessingLogs extends Command
{
    protected $signature = 'processing:logs {--limit=20 : Jumlah proses terakhir yang ditampilkan}';

    protected $description = 'Tampilkan ringkasan log sinkronisasi cuaca, iklim, dan berita';

    public function handle(): int
    {
        $limit = max(1, min(100, (int) $this->option('limit')));
        $logs = DB::table('processing_logs')->orderByDesc('started_at')->limit($limit)->get();

        $this->table(
            ['Mulai', 'Proses', 'Wilayah/Cakupan', 'Status', 'Diperiksa', 'Berhasil', 'Gagal', 'Ditulis', 'Pesan'],
            $logs->map(fn ($log) => [
                $log->started_at,
                $log->process,
                $log->scope ?? '—',
                $log->status,
                $log->records_processed,
                $log->records_succeeded,
                $log->records_failed,
                $log->records_written,
                $log->error_message ? mb_substr($log->error_message, 0, 100) : '—',
            ])->all(),
        );

        return self::SUCCESS;
    }
}
