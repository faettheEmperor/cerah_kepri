<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Windows Task Scheduler must call php artisan schedule:run every minute.
// These tasks use WIB, as configured for CERAH.
Schedule::command('weather:aggregate-daily')->everyThirtyMinutes()->withoutOverlapping();
Schedule::command('news:sync')->everyThirtyMinutes()->withoutOverlapping();
Schedule::command('weather:prune')->hourly()->withoutOverlapping();

// ERA5 history generally lags several days. One daily refresh is enough.
// Choose a time when your Windows laptop is usually switched on.
Schedule::command('climate:sync-history --recent-days=10')
    ->dailyAt('08:30')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping(180);

// Full history is intentionally manual: very large data/API workload.
// Example: php artisan climate:sync-history --years=10

// Melengkapi tanggal yang belum tersedia di ERA5; prakiraan hari ini belum final.
// Task Scheduler Windows harus berjalan dan PostgreSQL harus aktif.
Schedule::command('climate:sync-recent')
    ->dailyAt('09:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping(180);
Schedule::command('climate:sync-recent')
    ->dailyAt('15:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping(180);

// Data peta memakai weather_observations, bukan climate_daily_summaries.
// Dijalankan sekali sehari untuk semua wilayah (507 lokasi),
// agar tidak membebani API. Laptop, koneksi internet, dan PostgreSQL harus aktif.
Schedule::command('weather:sync')
    ->dailyAt('00:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping(240);
