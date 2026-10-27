<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateDailyWeather extends Command
{
    protected $signature = 'weather:aggregate-daily {--date= : Tanggal lokal yang akan dihitung (YYYY-MM-DD)}';
    protected $description = 'Agregasikan pengamatan cuaca current menjadi ringkasan harian';

    public function handle(): int
    {
        $today = now('Asia/Jakarta')->toDateString();
        $dates = $this->option('date') ? [$this->option('date')] : [now('Asia/Jakarta')->subDay()->toDateString(), $today];
        foreach ($dates as $date) {
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                $this->error('Format tanggal harus YYYY-MM-DD.');
                return self::FAILURE;
            }
        }

        $totalWritten = 0;
        foreach ($dates as $date) {
            $totalWritten += $this->aggregateDate($date);
        }

        return self::SUCCESS;
    }

    private function aggregateDate(string $date): int
    {
        $start = Carbon::parse($date, 'Asia/Jakarta')->startOfDay()->utc();
        $end = (clone $start)->addDay();
        $regions = DB::table('regions')->where('active', true)->orderBy('code')->get();
        $written = 0;

        foreach ($regions as $region) {
            // A sync run can contain duplicate current rows; count only the latest per observation timestamp.
            $observations = DB::table('weather_observations as w')
                ->joinSub(
                    DB::table('weather_observations')->select('region_code', 'valid_at')->selectRaw('MAX(fetched_at) AS fetched_at')
                        ->where('region_code', $region->code)->where('kind', 'current')->where('valid_at', '>=', $start)->where('valid_at', '<', $end)
                        ->groupBy('region_code', 'valid_at'),
                    'latest', fn ($join) => $join->on('latest.region_code', '=', 'w.region_code')->on('latest.valid_at', '=', 'w.valid_at')->on('latest.fetched_at', '=', 'w.fetched_at'),
                )
                ->where('w.kind', 'current')->orderBy('w.valid_at')->get([
                    'w.valid_at', 'w.temperature_c', 'w.weather_code', 'w.rain_mm',
                    'w.relative_humidity', 'w.pm2_5_ug_m3', 'w.us_aqi',
                ]);

            if ($observations->isEmpty()) {
                $this->warn("{$region->name}: tidak ada pengamatan untuk {$date}.");
                continue;
            }

            $temperatures = $observations->pluck('temperature_c')->filter(fn ($value) => $value !== null)->map(fn ($value) => (float) $value);
            $humidities = $observations->pluck('relative_humidity')->filter(fn ($value) => $value !== null)->map(fn ($value) => (float) $value);
            $pm25Values = $observations->pluck('pm2_5_ug_m3')->filter(fn ($value) => $value !== null)->map(fn ($value) => (float) $value);
            $aqiValues = $observations->pluck('us_aqi')->filter(fn ($value) => $value !== null)->map(fn ($value) => (float) $value);
            $codes = $observations->pluck('weather_code')->filter(fn ($value) => $value !== null)->countBy();
            $rainValues = $observations->pluck('rain_mm')->filter(fn ($value) => $value !== null)->map(fn ($value) => (float) $value);
            // Current precipitation/rain is a rate/depth for the current interval, not a daily total.
            // Extrapolate each reading across the gap to the next reading (cap at 90 minutes).
            $rainTotal = 0.0;
            for ($index = 0; $index < $observations->count(); $index++) {
                $value = $observations[$index]->rain_mm;
                if ($value === null) continue;
                $minutes = isset($observations[$index + 1])
                    ? min(90, max(0, Carbon::parse($observations[$index]->valid_at)->diffInMinutes(Carbon::parse($observations[$index + 1]->valid_at))))
                    : 30;
                $rainTotal += (float) $value * ($minutes / 60);
            }
            $code = null;
            if ($codes->isNotEmpty()) {
                $maxCount = $codes->max();
                $mostFrequentCodes = $codes->filter(fn ($count) => $count === $maxCount)->keys();
                $code = (int) $observations->reverse()->first(fn ($row) => in_array((string) $row->weather_code, $mostFrequentCodes->map(fn ($value) => (string) $value)->all(), true))?->weather_code;
            }
            $hasRainCoverage = $rainValues->count() >= (int) ceil($observations->count() * 0.75);

            DB::table('climate_daily_summaries')->upsert([[
                'region_code' => $region->code,
                'observed_on' => $date,
                'precipitation_mm' => $hasRainCoverage ? round($rainTotal, 2) : null,
                'temperature_mean_c' => $temperatures->isNotEmpty() ? round($temperatures->avg(), 2) : null,
                'relative_humidity_mean' => $humidities->isNotEmpty() ? (int) round($humidities->avg()) : null,
                'pm2_5_mean_ug_m3' => $pm25Values->isNotEmpty() ? round($pm25Values->avg(), 2) : null,
                'us_aqi_mean' => $aqiValues->isNotEmpty() ? round($aqiValues->avg(), 2) : null,
                'temperature_max_c' => $temperatures->isNotEmpty() ? round($temperatures->max(), 2) : null,
                'temperature_min_c' => $temperatures->isNotEmpty() ? round($temperatures->min(), 2) : null,
                'weather_code' => $code,
                'source' => 'open_meteo_current',
                'created_at' => now(),
                'updated_at' => now(),
            ]], ['region_code', 'observed_on'], [
                'precipitation_mm', 'temperature_mean_c', 'temperature_max_c', 'temperature_min_c',
                'relative_humidity_mean', 'pm2_5_mean_ug_m3', 'us_aqi_mean', 'weather_code', 'source', 'updated_at',
            ]);

            $written++;
            $this->info("{$region->name}: {$observations->count()} pengamatan diringkas.");
        }

        $this->info("{$written} ringkasan harian untuk {$date} disimpan.");
        return $written;
    }
}
