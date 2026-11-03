<?php

namespace App\Console\Commands;

use App\Services\ProcessingLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Mengisi jeda ERA5 sampai hari ini menggunakan Forecast API Open-Meteo.
 * Hari ini adalah prakiraan harian (belum final); HTH dikecualikan dari hari ini.
 */
class SyncRecentDaily extends Command
{
    protected $signature = 'climate:sync-recent {--region= : Hanya satu kode wilayah untuk uji coba}';
    protected $description = 'Lengkapi data harian sampai hari ini dengan model terbaru Open-Meteo';

    public function handle(): int
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $scope = trim((string) $this->option('region'));
        $query = DB::table('regions')->where('active', true)->orderBy('code');
        if ($scope !== '') {
            $query->where('code', $scope);
        }
        $regions = $query->get();
        if ($regions->isEmpty()) {
            $this->error('Wilayah tidak ditemukan.');
            return self::FAILURE;
        }

        $logId = ProcessingLog::start('climate_recent', $scope ?: 'Seluruh wilayah aktif', ['today' => $today]);
        $processed = 0;
        $succeeded = 0;
        $failed = 0;
        $written = 0;
        try {
            foreach ($regions->chunk(10) as $batchNumber => $batch) {
                $processed += $batch->count();
                try {
                    $response = Http::acceptJson()->connectTimeout(10)->timeout(90)
                        ->get('https://api.open-meteo.com/v1/forecast', [
                            'latitude' => $batch->pluck('latitude')->implode(','),
                            'longitude' => $batch->pluck('longitude')->implode(','),
                            'past_days' => 7,
                            'forecast_days' => 1,
                            'daily' => 'precipitation_sum,temperature_2m_mean,temperature_2m_max,temperature_2m_min,weather_code,relative_humidity_2m_mean',
                            'timezone' => 'Asia/Jakarta',
                        ]);
                    if (! $response->successful()) {
                        throw new \RuntimeException('HTTP '.$response->status().' dari Open-Meteo: '.mb_substr((string) $response->body(), 0, 160));
                    }
                    $payload = $response->json();
                    $locations = isset($payload[0]) && is_array($payload[0]) ? $payload : [$payload];
                    if (count($locations) !== $batch->count()) {
                        throw new \RuntimeException('Jumlah lokasi dari API tidak sesuai permintaan.');
                    }
                    $batchValues = $batch->values();
                    // Cegah mengubah data reanalisis ERA5 yang lebih berkualitas historis.
                    $existing = DB::table('climate_daily_summaries')
                        ->whereIn('region_code', $batch->pluck('code')->all())
                        ->whereBetween('observed_on', [Carbon::parse($today)->subDays(7)->toDateString(), $today])
                        ->get(['region_code', 'observed_on', 'source'])
                        ->keyBy(fn ($item) => $item->region_code.'|'.$item->observed_on);
                    $rows = [];
                    foreach ($batchValues as $idx => $region) {
                        $daily = $locations[$idx]['daily'] ?? [];
                        if (empty($daily['time']) || ! is_array($daily['time'])) {
                            $failed++;
                            $this->warn($region->code.' : data harian API kosong.');
                            continue;
                        }
                        $regionSucceeded = false;
                        foreach ($daily['time'] as $i => $date) {
                            if ($date > $today) continue;
                            $prior = $existing->get($region->code.'|'.$date);
                            if ($prior && str_contains((string) $prior->source, 'era5')) {
                                $regionSucceeded = true;
                                continue;
                            }
                            $precipitation = $daily['precipitation_sum'][$i] ?? null;
                            if ($precipitation === null) continue;
                            $source = $date === $today ? 'open_meteo_today_forecast' : 'open_meteo_recent_model';
                            $rows[] = [
                                'region_code' => $region->code,
                                'observed_on' => $date,
                                'precipitation_mm' => $precipitation,
                                'temperature_mean_c' => $daily['temperature_2m_mean'][$i] ?? null,
                                'temperature_max_c' => $daily['temperature_2m_max'][$i] ?? null,
                                'temperature_min_c' => $daily['temperature_2m_min'][$i] ?? null,
                                'weather_code' => $daily['weather_code'][$i] ?? null,
                                'relative_humidity_mean' => isset($daily['relative_humidity_2m_mean'][$i])
                                    ? (int) round((float) $daily['relative_humidity_2m_mean'][$i]) : null,
                                'source' => $source,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            $regionSucceeded = true;
                        }
                        if ($regionSucceeded) $succeeded++;
                        else {
                            $failed++;
                            $this->warn($region->code.' : tidak ada data yang dapat digunakan.');
                        }
                    }
                    if ($rows !== []) {
                        foreach (array_chunk($rows, 500) as $chunk) {
                            DB::table('climate_daily_summaries')->upsert($chunk, ['region_code', 'observed_on'], [
                                'precipitation_mm', 'temperature_mean_c', 'temperature_max_c', 'temperature_min_c',
                                'weather_code', 'relative_humidity_mean', 'source', 'updated_at',
                            ]);
                            $written += count($chunk);
                        }
                    }
                    $this->info('Batch '.($batchNumber + 1).': '.$batch->count().' wilayah diproses; '.$written.' baris telah ditulis.');
                } catch (Throwable $e) {
                    $failed += $batch->count();
                    $this->error('Batch '.($batchNumber + 1).': '.$e->getMessage());
                    if (str_contains($e->getMessage(), 'HTTP 429')) {
                        $this->warn('Kuota API tercapai. Tidak melanjutkan permintaan lain.');
                        break;
                    }
                }
                if ($scope === '') sleep(2);
            }
            ProcessingLog::finish($logId, $processed, $succeeded, $failed, $written,
                ['today' => $today, 'sources' => ['open_meteo_recent_model', 'open_meteo_today_forecast']]);
            $this->info("Selesai: {$written} baris ditulis; {$succeeded} wilayah berhasil; {$failed} gagal.");
            return $failed === 0 ? self::SUCCESS : self::FAILURE;
        } catch (Throwable $e) {
            ProcessingLog::fail($logId, $e, $processed, $written);
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
