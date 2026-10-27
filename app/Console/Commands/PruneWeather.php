<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneWeather extends Command
{
    protected $signature = 'weather:prune';
    protected $description = 'Hapus snapshot Open-Meteo yang berumur lebih dari 24 jam';

    public function handle(): int
    {
        $cutoff = now()->subDay();
        $weather = DB::table('weather_observations')->where('fetched_at', '<', $cutoff)->delete();
        $logs = DB::table('processing_logs')->where('started_at', '<', now()->subDays(90))->delete();

        $this->info("Dihapus: {$weather} snapshot cuaca berusia lebih dari 24 jam dan {$logs} log proses berusia lebih dari 90 hari.");

        return self::SUCCESS;
    }
}
