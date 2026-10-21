<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class SyncWeather extends Command
{
    protected $signature = 'weather:sync';
    protected $description = 'Ambil kondisi saat ini dan prakiraan Open-Meteo untuk wilayah Tanjungpinang';

    public function handle(): int
    {
        $failed = false;
        $failed |= ! $this->syncSource('weather', fn () => $this->syncWeather());
        if (DB::table('regions')->where('code', '21.72')->exists()) {
            $failed |= ! $this->syncSource('marine', fn () => $this->syncMarine());
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function syncSource(string $source, callable $callback): bool
    {
        $startedAt = now();
        $runId = DB::table('weather_sync_runs')->insertGetId([
            'source' => $source,
            'started_at' => $startedAt,
            'status' => 'running',
            'created_at' => $startedAt,
            'updated_at' => $startedAt,
        ]);

        try {
            $records = $callback();
            DB::table('weather_sync_runs')->where('id', $runId)->update([
                'finished_at' => now(),
                'status' => 'succeeded',
                'records_written' => $records,
                'updated_at' => now(),
            ]);
            $this->info("{$source}: {$records} baris disimpan.");

            return true;
        } catch (Throwable $exception) {
            report($exception);
            DB::table('weather_sync_runs')->where('id', $runId)->update([
                'finished_at' => now(),
                'status' => 'failed',
                'error_message' => mb_substr($exception->getMessage(), 0, 60000),
                'updated_at' => now(),
            ]);
            $this->error("{$source}: {$exception->getMessage()}");

            return false;
        }
    }

    private function syncWeather(): int
    {
        $regions = DB::table('regions')->where('active', true)->orderBy('code')->get();
        $latitude = $regions->pluck('latitude')->implode(',');
        $longitude = $regions->pluck('longitude')->implode(',');
        $variables = [
            'temperature_2m', 'relative_humidity_2m', 'apparent_temperature', 'precipitation',
            'rain', 'precipitation_probability', 'weather_code', 'wind_speed_10m',
            'wind_direction_10m', 'visibility', 'uv_index', 'soil_moisture_0_to_1cm',
            'et0_fao_evapotranspiration', 'is_day',
        ];
        $airVariables = ['pm2_5'];

        $response = Http::acceptJson()->timeout(45)->retry(3, 1000)->get('https://api.open-meteo.com/v1/forecast', [
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
        ])->throw();

        $airResponse = Http::acceptJson()->timeout(45)->retry(3, 1000)->get('https://air-quality-api.open-meteo.com/v1/air-quality', [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'current' => 'pm2_5',
            'hourly' => implode(',', $airVariables),
            'forecast_hours' => 168,
            'timezone' => 'Asia/Jakarta',
        ])->throw();

        $payload = $response->json();
        $airPayload = $airResponse->json();
        $locations = isset($payload[0]) ? $payload : [$payload];
        $airLocations = isset($airPayload[0]) ? $airPayload : [$airPayload];
        $fetchedAt = now();
        $rows = [];

        foreach ($regions->values() as $index => $region) {
            $data = $locations[$index] ?? null;
            $airData = $airLocations[$index] ?? [];
            if (! is_array($data) || empty($data['current']['time'])) {
                throw new \RuntimeException("Respons cuaca untuk {$region->name} tidak lengkap.");
            }

            $current = $data['current'];
            $current['pm2_5'] = $airData['current']['pm2_5'] ?? null;
            $daily = $data['daily'] ?? [];
            $today = substr($current['time'], 0, 10);
            $dailyIndex = array_search($today, $daily['time'] ?? [], true);
            $current['sunrise'] = $dailyIndex === false ? null : ($daily['sunrise'][$dailyIndex] ?? null);
            $current['sunset'] = $dailyIndex === false ? null : ($daily['sunset'][$dailyIndex] ?? null);
            $rows[] = $this->weatherRow($region->code, 'current', $current['time'], $fetchedAt, $current);

            $hourly = $data['hourly'] ?? [];
            $airHourly = $airData['hourly'] ?? [];
            foreach ($hourly['time'] ?? [] as $hourIndex => $time) {
                $hour = [];
                foreach ($variables as $variable) {
                    $hour[$variable] = $hourly[$variable][$hourIndex] ?? null;
                }
                $airForecastIndex = array_search($time, $airHourly['time'] ?? [], true);
                $hour['pm2_5'] = $airForecastIndex === false ? null : ($airHourly['pm2_5'][$airForecastIndex] ?? null);
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

        return count($rows);
    }

    private function syncMarine(): int
    {
        $city = DB::table('regions')->where('code', '21.72')->first();
        $response = Http::acceptJson()->timeout(45)->retry(3, 1000)->get('https://marine-api.open-meteo.com/v1/marine', [
            'latitude' => $city->latitude,
            'longitude' => $city->longitude,
            'current' => implode(',', ['wave_height', 'wave_direction', 'wave_period', 'sea_surface_temperature']),
            'hourly' => implode(',', [
                'wave_height', 'wave_direction', 'wave_period', 'wind_wave_height', 'swell_wave_height',
                'sea_surface_temperature', 'ocean_current_velocity', 'ocean_current_direction',
            ]),
            'forecast_hours' => 168,
            'timezone' => 'Asia/Jakarta',
            'length_unit' => 'metric',
            'wind_speed_unit' => 'kmh',
        ])->throw();

        $data = $response->json();
        $fetchedAt = now();
        $rows = [];
        if (! empty($data['current']['time'])) {
            $rows[] = $this->marineRow('current', $data['current']['time'], $fetchedAt, $data['current']);
        }

        $hourly = $data['hourly'] ?? [];
        foreach ($hourly['time'] ?? [] as $index => $time) {
            $hour = [];
            foreach ([
                'wave_height', 'wave_direction', 'wave_period', 'wind_wave_height', 'swell_wave_height',
                'sea_surface_temperature', 'ocean_current_velocity', 'ocean_current_direction',
            ] as $variable) {
                $hour[$variable] = $hourly[$variable][$index] ?? null;
            }
            $rows[] = $this->marineRow('forecast', $time, $fetchedAt, $hour);
        }

        if ($rows === []) {
            throw new \RuntimeException('Respons prakiraan laut kosong.');
        }

        DB::table('marine_observations')->insert($rows);

        return count($rows);
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

    private function marineRow(string $kind, string $time, mixed $fetchedAt, array $data): array
    {
        return [
            'region_code' => '21.72',
            'kind' => $kind,
            'valid_at' => Carbon::parse($time, 'Asia/Jakarta'),
            'fetched_at' => $fetchedAt,
            'wave_height_m' => $data['wave_height'] ?? null,
            'wave_direction_deg' => $data['wave_direction'] ?? null,
            'wave_period_s' => $data['wave_period'] ?? null,
            'wind_wave_height_m' => $data['wind_wave_height'] ?? null,
            'swell_wave_height_m' => $data['swell_wave_height'] ?? null,
            'sea_surface_temperature_c' => $data['sea_surface_temperature'] ?? null,
            'ocean_current_kmh' => $data['ocean_current_velocity'] ?? null,
            'ocean_current_direction_deg' => $data['ocean_current_direction'] ?? null,
            'created_at' => $fetchedAt,
            'updated_at' => $fetchedAt,
        ];
    }
}
