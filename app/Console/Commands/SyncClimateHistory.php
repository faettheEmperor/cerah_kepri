<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use App\Services\ProcessingLog;
use Throwable;

class SyncClimateHistory extends Command
{
    protected $signature = 'climate:sync-history {--years=10 : Jumlah tahun historis yang disimpan} {--recent-days=0 : Sinkronkan jendela historis terbaru yang pendek}';
    protected $description = 'Sinkronkan hujan dan suhu historis harian dari Open-Meteo ERA5';

    public function handle(): int
    {
        $logId = ProcessingLog::start('climate_history', 'Riwayat historis Open-Meteo');
        $years = max(1, min(25, (int) $this->option('years')));
        $recentDays = max(0, min(14, (int) $this->option('recent-days')));
        $today = Carbon::now('Asia/Jakarta');
        $endDate = $today->copy()->subDays(6);
        $startDate = $recentDays > 0
            ? $endDate->copy()->subDays($recentDays - 1)
            : $today->copy()->subYears($years)->startOfYear();
        $start = $startDate->toDateString();
        $end = $endDate->toDateString();
        $regions = DB::table('regions')->where('active', true)->orderBy('code')->get();
        $expectedDays = Carbon::parse($start)->diffInDays(Carbon::parse($end)) + 1;
        $completedCodes = $recentDays > 0
            ? collect()
            : DB::table('climate_daily_summaries')
                ->select('region_code')
                ->whereBetween('observed_on', [$start, $end])
                ->whereNotNull('precipitation_mm')
                ->whereNotNull('relative_humidity_mean')
                ->groupBy('region_code')
                ->havingRaw('COUNT(DISTINCT observed_on) >= ?', [$expectedDays])
                ->pluck('region_code');
        $regions = $regions->reject(fn ($region) => $completedCodes->contains($region->code))->values();
        $written = 0;
        $failed = 0;
        $processed = 0;
        $hourlyLimitReached = false;

        $this->info($recentDays > 0
            ? "Memperbarui {$recentDays} hari terbaru yang tersedia dari Open-Meteo ({$start} sampai {$end})."
            : ($completedCodes->isNotEmpty()
                ? $completedCodes->count().' wilayah sudah lengkap; melanjutkan wilayah yang belum lengkap.'
                : 'Memulai sinkronisasi untuk seluruh wilayah aktif.'));

        foreach ($regions->chunk(10) as $batchIndex => $batch) {
            $processed += $batch->count();
            try {
                // One archive request can return multiple coordinates. Small
                // batches keep the ten-year response size manageable in XAMPP.
                $response = Http::acceptJson()->timeout(90)->retry(
                    5,
                    fn (int $attempt, Throwable $exception) => $exception instanceof RequestException && $exception->response?->status() === 429
                        ? min(60_000, 10_000 * $attempt)
                        : min(10_000, 1500 * $attempt),
                    fn (Throwable $exception) => ! $this->isHourlyRateLimit($exception),
                )->get('https://archive-api.open-meteo.com/v1/archive', [
                    'latitude' => $batch->pluck('latitude')->implode(','),
                    'longitude' => $batch->pluck('longitude')->implode(','),
                    'start_date' => $start,
                    'end_date' => $end,
                    'daily' => 'precipitation_sum,temperature_2m_mean,temperature_2m_max,temperature_2m_min,weather_code,relative_humidity_2m_mean',
                    'models' => 'era5',
                    'timezone' => 'Asia/Jakarta',
                ])->throw();

                $locations = $this->normalizeLocations($response->json(), $batch->count());
                foreach ($batch->values() as $index => $region) {
                    $daily = $locations[$index]['daily'] ?? null;
                    if (! is_array($daily) || empty($daily['time'])) {
                        $failed++;
                        $this->error("{$region->code} {$region->name}: Open-Meteo tidak mengembalikan data harian.");
                        continue;
                    }

                    $rows = [];
                    foreach ($daily['time'] as $dayIndex => $date) {
                        $rows[] = [
                            'region_code' => $region->code,
                            'observed_on' => $date,
                            'precipitation_mm' => $daily['precipitation_sum'][$dayIndex] ?? null,
                            'temperature_mean_c' => $daily['temperature_2m_mean'][$dayIndex] ?? null,
                            'relative_humidity_mean' => isset($daily['relative_humidity_2m_mean'][$dayIndex]) ? (int) round((float) $daily['relative_humidity_2m_mean'][$dayIndex]) : null,
                            'temperature_max_c' => $daily['temperature_2m_max'][$dayIndex] ?? null,
                            'temperature_min_c' => $daily['temperature_2m_min'][$dayIndex] ?? null,
                            'weather_code' => $daily['weather_code'][$dayIndex] ?? null,
                            'source' => 'open_meteo_era5',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    if (count(array_filter(array_column($rows, 'precipitation_mm'), fn ($value) => $value !== null)) === 0) {
                        $failed++;
                        $this->error("{$region->code} {$region->name}: curah hujan kosong; data wilayah ini tidak disimpan.");
                        continue;
                    }

                    foreach (array_chunk($rows, 500) as $chunk) {
                        DB::table('climate_daily_summaries')->upsert(
                            $chunk,
                            ['region_code', 'observed_on'],
                            ['precipitation_mm', 'temperature_mean_c', 'temperature_max_c', 'temperature_min_c', 'relative_humidity_mean', 'weather_code', 'source', 'updated_at'],
                        );
                        $written += count($chunk);
                    }
                    $this->info("{$region->code} {$region->name}: ".count($rows).' hari historis diperbarui.');
                }
            } catch (Throwable $exception) {
                $failed += $batch->count();
                $names = $batch->map(fn ($region) => $region->code.' '.$region->name)->implode(', ');
                $this->error('Kelompok '.($batchIndex + 1).' ('.$names.'): '.$exception->getMessage());
                if ($this->isHourlyRateLimit($exception)) {
                    $hourlyLimitReached = true;
                    $this->warn('Kuota per jam habis. Proses dihentikan agar kelompok berikutnya tidak ikut gagal. Data yang sudah tersimpan aman; jalankan perintah ini lagi setelah kuota tersedia.');
                    break;
                }
                if (str_contains($exception->getMessage(), '429')) {
                    sleep(60);
                }
            }

            // Keep archive requests comfortably below public API rate limits.
            usleep(2_000_000);
        }

        $this->info("Selesai. {$written} ringkasan harian ditulis ({$start} sampai {$end}); {$failed} wilayah pada kelompok yang gagal.");
        ProcessingLog::finish($logId, $processed, max(0, $processed - $failed), $failed, $written, [
            'start_date' => $start,
            'end_date' => $end,
            'years' => $years,
            'recent_days' => $recentDays,
            'hourly_limit_reached' => $hourlyLimitReached,
        ]);

        return $failed === 0 && ! $hourlyLimitReached ? self::SUCCESS : self::FAILURE;
    }

    private function isHourlyRateLimit(Throwable $exception): bool
    {
        if (! $exception instanceof RequestException || $exception->response?->status() !== 429) {
            return false;
        }

        return str_contains(
            (string) $exception->response->json('reason'),
            'Hourly API request limit exceeded',
        );
    }

    private function normalizeLocations(array $payload, int $expected): array
    {
        $locations = isset($payload[0]) && is_array($payload[0]) ? $payload : [$payload];
        if (count($locations) !== $expected) {
            throw new \RuntimeException('Open-Meteo mengembalikan '.count($locations)." lokasi; seharusnya {$expected}.");
        }

        return array_values($locations);
    }
}
