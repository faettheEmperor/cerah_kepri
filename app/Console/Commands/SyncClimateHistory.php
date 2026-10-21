<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class SyncClimateHistory extends Command
{
    protected $signature = 'climate:sync-history {--years=10 : Jumlah tahun historis yang disimpan}';
    protected $description = 'Sinkronkan hujan dan suhu historis harian dari Open-Meteo ERA5';

    public function handle(): int
    {
        $years = max(1, min(25, (int) $this->option('years')));
        $start = Carbon::now('Asia/Jakarta')->subYears($years)->startOfYear()->toDateString();
        $end = Carbon::now('Asia/Jakarta')->subDays(6)->toDateString();
        $regions = DB::table('regions')->where('active', true)->where('type', 'village')->orderBy('code')->get();
        $written = 0;
        $failed = 0;

        foreach ($regions as $region) {
            try {
                // ERA5 provides daily precipitation. ERA5-Land does not expose it.
                $response = Http::acceptJson()->timeout(90)->retry(
                    5,
                    fn (int $attempt, \Illuminate\Http\Client\RequestException $exception) => $exception->response?->status() === 429
                        ? min(60_000, 10_000 * $attempt)
                        : min(10_000, 1500 * $attempt),
                )->get('https://archive-api.open-meteo.com/v1/archive', [
                    'latitude' => $region->latitude,
                    'longitude' => $region->longitude,
                    'start_date' => $start,
                    'end_date' => $end,
                    'daily' => 'precipitation_sum,temperature_2m_mean',
                    'models' => 'era5',
                    'timezone' => 'Asia/Jakarta',
                ])->throw();

                $location = $response->json();
                $daily = $location['daily'] ?? null;
                if (! is_array($daily) || empty($daily['time'])) {
                    throw new \RuntimeException('Open-Meteo tidak mengembalikan data harian. Respons: '.json_encode($location));
                }

                $rows = [];
                foreach ($daily['time'] as $dayIndex => $date) {
                    $rows[] = [
                        'region_code' => $region->code,
                        'observed_on' => $date,
                        'precipitation_mm' => $daily['precipitation_sum'][$dayIndex] ?? null,
                        'temperature_mean_c' => $daily['temperature_2m_mean'][$dayIndex] ?? null,
                        'source' => 'open_meteo_era5',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                if (count(array_filter(array_column($rows, 'precipitation_mm'), fn ($value) => $value !== null)) === 0) {
                    throw new \RuntimeException('Curah hujan semuanya kosong; data wilayah ini tidak disimpan.');
                }

                foreach (array_chunk($rows, 500) as $chunk) {
                    DB::table('climate_daily_summaries')->upsert(
                        $chunk,
                        ['region_code', 'observed_on'],
                        ['precipitation_mm', 'temperature_mean_c', 'source', 'updated_at'],
                    );
                    $written += count($chunk);
                }
                $this->info("{$region->name}: ".count($rows).' hari historis diperbarui.');
            } catch (Throwable $exception) {
                $failed++;
                $this->error("{$region->name}: {$exception->getMessage()}");
                if (str_contains($exception->getMessage(), '429')) {
                    sleep(60);
                }
            }

            // Public API request limit: keep calls spaced apart and retry failed locations later.
            usleep(4_000_000);
        }

        $this->info("Selesai. {$written} ringkasan harian disimpan ({$start} sampai {$end}); {$failed} kelurahan gagal dan bisa dicoba ulang.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
