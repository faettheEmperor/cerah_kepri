<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Services\ProcessingLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class SyncWeather extends Command
{
    protected $signature = 'weather:sync {--region= : Sinkronkan satu wilayah berdasarkan kode wilayah}';
    protected $description = 'Ambil kondisi cuaca dan prakiraan Open-Meteo untuk seluruh atau satu wilayah Kepulauan Riau';

    public function handle(): int
    {
        $regionCode = trim((string) $this->option('region'));
        $regionCode = $regionCode !== '' ? $regionCode : null;
        if ($regionCode !== null && ! DB::table('regions')->where('code', $regionCode)->where('active', true)->exists()) {
            $this->error("Wilayah aktif dengan kode {$regionCode} tidak ditemukan.");

            return self::FAILURE;
        }

        $failed = false;
        $failed |= ! $this->syncSource('weather', fn () => $this->syncWeather($regionCode));

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function syncSource(string $source, callable $callback): bool
    {
        $scope = trim((string) $this->option('region')) ?: 'Seluruh wilayah aktif';
        $runId = ProcessingLog::start('weather', $scope);

        try {
            $result = $callback();
            $metrics = is_array($result) ? $result : ['processed' => 1, 'succeeded' => 1, 'failed' => 0, 'written' => (int) $result];
            ProcessingLog::finish($runId, $metrics['processed'], $metrics['succeeded'], $metrics['failed'], $metrics['written']);
            $this->info("{$source}: {$metrics['written']} baris disimpan; {$metrics['succeeded']}/{$metrics['processed']} berhasil, {$metrics['failed']} gagal.");

            return true;
        } catch (Throwable $exception) {
            report($exception);
            ProcessingLog::fail($runId, $exception, 1);
            $this->error("{$source}: {$exception->getMessage()}");

            return false;
        }
    }

    private function syncWeather(?string $regionCode = null): array
    {
        $regionsQuery = DB::table('regions')->where('active', true)->orderBy('code');
        if ($regionCode !== null) {
            $regionsQuery->where('code', $regionCode);
        }
        $regions = $regionsQuery->get();
        if ($regions->isEmpty()) {
            throw new \RuntimeException('Belum ada wilayah aktif. Jalankan seeder wilayah Kepulauan Riau terlebih dahulu.');
        }
        $variables = [
            'temperature_2m', 'relative_humidity_2m', 'apparent_temperature', 'precipitation',
            'rain', 'precipitation_probability', 'weather_code', 'wind_speed_10m',
            'wind_direction_10m', 'visibility', 'uv_index', 'soil_moisture_0_to_1cm',
            'et0_fao_evapotranspiration', 'is_day',
        ];
        $airVariables = ['pm2_5', 'us_aqi'];
        $fetchedAt = now();
        $written = 0;
        // Keep requests spread out. The free Open-Meteo limit is applied by IP,
        // so a second manual/scheduled run can otherwise exhaust it quickly.
        $batches = $regions->chunk(10);

        foreach ($batches as $batchIndex => $batch) {
            $latitude = $batch->pluck('latitude')->implode(',');
            $longitude = $batch->pluck('longitude')->implode(',');
            $weatherPayload = $this->openMeteoGet('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => implode(',', [
                    'temperature_2m', 'relative_humidity_2m', 'apparent_temperature', 'precipitation',
                    'rain', 'weather_code', 'wind_speed_10m', 'wind_direction_10m', 'visibility', 'uv_index', 'is_day',
                ]),
                'hourly' => implode(',', $variables),
                'daily' => 'sunrise,sunset',
                'forecast_days' => 7,
                'forecast_hours' => 168,
                'timezone' => 'Asia/Jakarta',
                'wind_speed_unit' => 'kmh',
            ], $regionCode === null);
            // Avoid sending the weather and air-quality requests back-to-back.
            sleep(2);
            $airPayload = $this->openMeteoGet('https://air-quality-api.open-meteo.com/v1/air-quality', [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'current' => implode(',', $airVariables),
                'hourly' => implode(',', $airVariables),
                'forecast_hours' => 168,
                'timezone' => 'Asia/Jakarta',
            ], $regionCode === null);

            $locations = $this->normalizeLocations($weatherPayload, $batch->count());
            $airLocations = $this->normalizeLocations($airPayload, $batch->count());
            $rows = [];
            foreach ($batch->values() as $index => $region) {
                $data = $locations[$index] ?? null;
                $airData = $airLocations[$index] ?? [];
                if (! is_array($data) || empty($data['current']['time'])) {
                    throw new \RuntimeException("Respons cuaca untuk {$region->name} tidak lengkap (kelompok ".($batchIndex + 1).').');
                }

                $current = $data['current'];
                $current['pm2_5'] = $airData['current']['pm2_5'] ?? null;
                $current['us_aqi'] = $airData['current']['us_aqi'] ?? null;
                $daily = $data['daily'] ?? [];
                $today = substr($current['time'], 0, 10);
                $dailyIndex = array_search($today, $daily['time'] ?? [], true);
                $current['sunrise'] = $dailyIndex === false ? null : ($daily['sunrise'][$dailyIndex] ?? null);
                $current['sunset'] = $dailyIndex === false ? null : ($daily['sunset'][$dailyIndex] ?? null);
                $rows[] = $this->weatherRow($region->code, 'current', $current['time'], $fetchedAt, $current);

                $hourly = $data['hourly'] ?? [];
                $airHourly = $airData['hourly'] ?? [];
                $airIndexes = array_flip($airHourly['time'] ?? []);
                foreach ($hourly['time'] ?? [] as $hourIndex => $time) {
                    $hour = [];
                    foreach ($variables as $variable) {
                        $hour[$variable] = $hourly[$variable][$hourIndex] ?? null;
                    }
                    $airForecastIndex = $airIndexes[$time] ?? null;
                    $hour['pm2_5'] = $airForecastIndex === null ? null : ($airHourly['pm2_5'][$airForecastIndex] ?? null);
                    $hour['us_aqi'] = $airForecastIndex === null ? null : ($airHourly['us_aqi'][$airForecastIndex] ?? null);
                    $date = substr($time, 0, 10);
                    $forecastDailyIndex = array_search($date, $daily['time'] ?? [], true);
                    $hour['sunrise'] = $forecastDailyIndex === false ? null : ($daily['sunrise'][$forecastDailyIndex] ?? null);
                    $hour['sunset'] = $forecastDailyIndex === false ? null : ($daily['sunset'][$forecastDailyIndex] ?? null);
                    $rows[] = $this->weatherRow($region->code, 'forecast', $time, $fetchedAt, $hour);
                }
            }

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('weather_observations')->insert($chunk);
            }
            $written += count($rows);
            $this->line('Kelompok '.($batchIndex + 1).'/'.$batches->count().': '.$batch->count().' wilayah diproses.');

            if ($batchIndex + 1 < $batches->count()) {
                sleep(3);
            }
        }

        // The page only reads the latest forecast run. Keep current observations
        // for the requested one-day history, but discard superseded 7-day runs.
        $oldForecasts = DB::table('weather_observations')->where('kind', 'forecast')->where('fetched_at', '<', $fetchedAt);
        if ($regionCode !== null) {
            $oldForecasts->where('region_code', $regionCode);
        }
        $oldForecasts->delete();

        return ['processed' => $regions->count(), 'succeeded' => $regions->count(), 'failed' => 0, 'written' => $written];
    }

    private function normalizeLocations(array $payload, int $expected): array
    {
        $locations = isset($payload[0]) && is_array($payload[0]) ? $payload : [$payload];
        if (count($locations) !== $expected) {
            throw new \RuntimeException('Open-Meteo mengembalikan '.count($locations)." lokasi; seharusnya {$expected}.");
        }

        return array_values($locations);
    }

    /**
     * Make an Open-Meteo request with a retry delay that respects HTTP 429.
     * The public endpoint asks callers to wait at least one minute when the
     * minute-level limit is reached.
     */
    private function openMeteoGet(string $url, array $query, bool $retryRateLimit = true): array
    {
        $maxAttempts = 5;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $response = Http::acceptJson()->timeout(90)->get($url, $query);
            } catch (Throwable $exception) {
                if ($attempt === $maxAttempts) {
                    throw $exception;
                }

                $delay = min(30, 3 * $attempt);
                $this->warn("Open-Meteo belum merespons; mencoba lagi dalam {$delay} detik ({$attempt}/{$maxAttempts}).");
                sleep($delay);
                continue;
            }

            if ($response->status() === 429) {
                if (! $retryRateLimit) {
                    $response->throw();
                }
                if ($attempt === $maxAttempts) {
                    $response->throw();
                }

                $delay = $this->retryAfterSeconds($response->header('Retry-After'));
                $this->warn("Batas permintaan Open-Meteo tercapai; menunggu {$delay} detik lalu mencoba kelompok yang sama ({$attempt}/{$maxAttempts}).");
                sleep($delay);
                continue;
            }

            if ($response->serverError() && $attempt < $maxAttempts) {
                $delay = min(30, 3 * $attempt);
                $this->warn("Open-Meteo sedang mengalami gangguan; mencoba lagi dalam {$delay} detik ({$attempt}/{$maxAttempts}).");
                sleep($delay);
                continue;
            }

            $response->throw();
            $payload = $response->json();
            if (! is_array($payload)) {
                throw new \RuntimeException('Open-Meteo mengembalikan respons JSON yang tidak valid.');
            }

            return $payload;
        }

        throw new \RuntimeException('Permintaan ke Open-Meteo gagal setelah beberapa kali dicoba.');
    }

    private function retryAfterSeconds(?string $retryAfter): int
    {
        if ($retryAfter !== null && is_numeric($retryAfter)) {
            return max(60, (int) ceil((float) $retryAfter));
        }

        if ($retryAfter !== null) {
            try {
                return max(60, now()->diffInSeconds(Carbon::parse($retryAfter), false));
            } catch (Throwable) {
                // Fall through to Open-Meteo's documented 60-second wait.
            }
        }

        return 60;
    }

    private function weatherRow(string $regionCode, string $kind, string $time, mixed $fetchedAt, array $data): array
    {
        return [
            'region_code' => $regionCode,
            'kind' => $kind,
            'valid_at' => Carbon::parse($time, 'Asia/Jakarta'),
            'fetched_at' => $fetchedAt,
            'temperature_c' => $data['temperature_2m'] ?? null,
            'relative_humidity' => $data['relative_humidity_2m'] ?? null,
            'wind_speed_kmh' => $data['wind_speed_10m'] ?? null,
            'wind_direction_deg' => $data['wind_direction_10m'] ?? null,
            'weather_code' => $data['weather_code'] ?? null,
            'is_day' => isset($data['is_day']) ? (bool) $data['is_day'] : null,
            'rain_mm' => $data['rain'] ?? $data['precipitation'] ?? null,
            'precipitation_probability' => $data['precipitation_probability'] ?? null,
            'visibility_m' => $data['visibility'] ?? null,
            'pm2_5_ug_m3' => $data['pm2_5'] ?? null,
            'us_aqi' => $data['us_aqi'] ?? null,
            'sunrise_at' => empty($data['sunrise']) ? null : Carbon::parse($data['sunrise'], 'Asia/Jakarta'),
            'sunset_at' => empty($data['sunset']) ? null : Carbon::parse($data['sunset'], 'Asia/Jakarta'),
            'apparent_temperature_c' => $data['apparent_temperature'] ?? null,
            'uv_index' => $data['uv_index'] ?? null,
            'soil_moisture_0_1cm' => $data['soil_moisture_0_to_1cm'] ?? null,
            'et0_mm' => $data['et0_fao_evapotranspiration'] ?? null,
            'created_at' => $fetchedAt,
            'updated_at' => $fetchedAt,
        ];
    }

}
