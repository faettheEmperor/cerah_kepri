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
        $marine = DB::table('marine_observations')->where('fetched_at', '<', $cutoff)->delete();
        $runs = DB::table('weather_sync_runs')->where('started_at', '<', $cutoff)->delete();

        $this->info("Dihapus: {$weather} cuaca, {$marine} laut, {$runs} catatan sinkronisasi.");

        return self::SUCCESS;
    }
}
