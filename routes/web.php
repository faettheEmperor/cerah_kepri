<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Services\ActivityAdvice;
use App\Services\ClimateInsight;

Route::get('/', function () {
    return view('weather', [
        'regionsEndpoint' => url('/api/weather/regions'),
    ]);
});

Route::get('/api/weather/regions', function () {
    return response()->json(DB::table('regions')->where('active', true)->orderBy('code')->get());
});

Route::get('/api/weather/map', function () {
    $latest = DB::table('weather_observations')->select('region_code')->selectRaw('MAX(fetched_at) AS latest_at')
        ->where('kind', 'current')->groupBy('region_code');
    $forecastRuns = DB::table('weather_observations')->select('region_code')->selectRaw('MAX(fetched_at) AS latest_at')
        ->where('kind', 'forecast')->groupBy('region_code');
    $nextForecast = DB::table('weather_observations as f')->joinSub($forecastRuns, 'fr', function ($join) {
        $join->on('fr.region_code', '=', 'f.region_code')->on('fr.latest_at', '=', 'f.fetched_at');
    })->where('f.valid_at', '>=', now())->select('f.region_code')->selectRaw('MIN(f.valid_at) AS next_at, MAX(f.fetched_at) AS latest_at')->groupBy('f.region_code');
    $points = DB::table('regions as r')
        ->leftJoinSub($latest, 'latest', 'latest.region_code', '=', 'r.code')
        ->leftJoin('weather_observations as w', function ($join) {
            $join->on('w.region_code', '=', 'r.code')->on('w.fetched_at', '=', 'latest.latest_at')->where('w.kind', '=', 'current');
        })
        ->leftJoinSub($nextForecast, 'next_forecast', 'next_forecast.region_code', '=', 'r.code')
        ->leftJoin('weather_observations as forecast', function ($join) {
            $join->on('forecast.region_code', '=', 'r.code')->on('forecast.valid_at', '=', 'next_forecast.next_at')->on('forecast.fetched_at', '=', 'next_forecast.latest_at')->where('forecast.kind', '=', 'forecast');
        })
        ->where('r.active', true)
        ->select('r.code', 'r.name', 'r.parent_code', 'r.latitude', 'r.longitude', 'w.temperature_c', 'w.weather_code', 'w.is_day', 'w.valid_at', 'w.rain_mm', 'w.wind_speed_kmh', 'w.pm2_5_ug_m3', 'forecast.precipitation_probability')
        ->orderBy('r.code')->get();

    return response()->json($points);
});

Route::get('/api/weather/regions/{code}', function (string $code, ActivityAdvice $activityAdvice, ClimateInsight $climateInsight) {
    $region = DB::table('regions')->where('code', $code)->where('active', true)->first();
    abort_unless($region, 404);

    $current = DB::table('weather_observations')->where('region_code', $code)->where('kind', 'current')->orderByDesc('fetched_at')->first();
    $latestFetchedAt = DB::table('weather_observations')->where('region_code', $code)->where('kind', 'current')->max('fetched_at');
    if ($current && $latestFetchedAt && $current->fetched_at !== $latestFetchedAt) {
        $latestCurrent = DB::table('weather_observations')->where('region_code', $code)->where('kind', 'current')->where('fetched_at', $latestFetchedAt)->first();
        if ($latestCurrent) $current = $latestCurrent;
    }
    if ($current && $latestFetchedAt && $current->fetched_at === $latestFetchedAt) {
        $currentForecast = DB::table('weather_observations')->where('region_code', $code)->where('kind', 'forecast')->where('fetched_at', $latestFetchedAt)->where('valid_at', $current->valid_at)->first();
        if ($currentForecast && ($current->pm2_5_ug_m3 === null || $current->pm2_5_ug_m3 === '')) {
            $current->pm2_5_ug_m3 = $currentForecast->pm2_5_ug_m3;
        }
    }
    $forecastRun = DB::table('weather_observations')->where('region_code', $code)->where('kind', 'forecast')->max('fetched_at');
    $forecast = $forecastRun
        ? DB::table('weather_observations')->where('region_code', $code)->where('kind', 'forecast')->where('fetched_at', $forecastRun)->orderBy('valid_at')->get()
        : collect();
    $coastalVillages = ['21.72.03.1001', '21.72.03.1003', '21.72.03.1004', '21.72.03.1005', '21.72.04.1004', '21.72.04.1005'];
    $hasCoastalAdvice = $region->type === 'city' || in_array($region->code, $coastalVillages, true);
    $marineCurrent = $hasCoastalAdvice
        ? DB::table('marine_observations')->where('region_code', '21.72')->where('kind', 'current')->orderByDesc('fetched_at')->first()
        : null;
    $marineRun = $region->type === 'city'
        ? DB::table('marine_observations')->where('region_code', '21.72')->where('kind', 'forecast')->max('fetched_at')
        : null;
    $marineForecast = $marineRun
        ? DB::table('marine_observations')->where('region_code', '21.72')->where('kind', 'forecast')->where('fetched_at', $marineRun)->orderBy('valid_at')->get()
        : collect();
    $adviceWeather = $current;
    if ($current && $forecast->isNotEmpty()) {
        $adviceWeather = (object) array_merge(
            (array) $forecast->first(),
            array_filter((array) $current, fn ($value) => $value !== null),
        );
    }
    if ($adviceWeather) {
        $adviceWeather->region_type = $region->type;
    }
    $next24Hours = $forecast->filter(fn ($row) => $row->valid_at >= now())->take(24)->values();
    if ($adviceWeather && $next24Hours->isNotEmpty()) {
        $peakRain = $next24Hours->sortByDesc(fn ($row) => (int) ($row->precipitation_probability ?? 0))->first();
        $eveningHours = $next24Hours->filter(fn ($row) => (int) \Illuminate\Support\Carbon::parse($row->valid_at)->timezone('Asia/Jakarta')->format('G') >= 15);
        $nightHours = $next24Hours->filter(fn ($row) => (int) \Illuminate\Support\Carbon::parse($row->valid_at)->timezone('Asia/Jakarta')->format('G') >= 18);
        $eveningPeak = $eveningHours->sortByDesc(fn ($row) => (int) ($row->precipitation_probability ?? 0))->first();
        $nightPeak = $nightHours->sortByDesc(fn ($row) => (int) ($row->precipitation_probability ?? 0))->first();
        $maxRainHour = $next24Hours->sortByDesc(fn ($row) => (float) ($row->rain_mm ?? 0))->first();
        $maxWindHour = $next24Hours->sortByDesc(fn ($row) => (float) ($row->wind_speed_kmh ?? 0))->first();
        $stormCodes = [95, 96, 99];
        $adviceWeather->peak_rain_probability = (int) ($peakRain->precipitation_probability ?? 0);
        $adviceWeather->peak_rain_at = $peakRain->valid_at;
        $adviceWeather->rain_chance_24h = (int) $next24Hours->max('precipitation_probability');
        $adviceWeather->peak_rain_mm = (float) ($peakRain->rain_mm ?? 0);
        $adviceWeather->max_rain_hourly_mm = (float) ($maxRainHour->rain_mm ?? 0);
        $adviceWeather->max_rain_hour_at = $maxRainHour->valid_at ?? null;
        $adviceWeather->rain_total_24h_mm = (float) $next24Hours->sum('rain_mm');
        $adviceWeather->rain_hours_over_1mm = $next24Hours->filter(fn ($row) => (float) ($row->rain_mm ?? 0) >= 1)->count();
        $adviceWeather->max_wind_24h_kmh = (float) $next24Hours->max('wind_speed_kmh');
        $adviceWeather->max_wind_direction_deg = $maxWindHour?->wind_direction_deg;
        $adviceWeather->min_visibility_24h_m = $next24Hours->filter(fn ($row) => $row->visibility_m !== null)->min('visibility_m');
        $adviceWeather->max_humidity_24h = $next24Hours->filter(fn ($row) => $row->relative_humidity !== null)->max('relative_humidity');
        $adviceWeather->max_pm2_5_24h = $next24Hours->filter(fn ($row) => $row->pm2_5_ug_m3 !== null)->max('pm2_5_ug_m3');
        $adviceWeather->storm_expected_24h = $next24Hours->contains(fn ($row) => in_array((int) $row->weather_code, $stormCodes, true));
        $adviceWeather->evening_rain_probability = (int) ($eveningHours->max('precipitation_probability') ?? 0);
        $adviceWeather->evening_rain_at = $eveningPeak?->valid_at;
        $adviceWeather->night_rain_probability = (int) ($nightHours->max('precipitation_probability') ?? 0);
        $adviceWeather->night_rain_at = $nightPeak?->valid_at;
        $adviceWeather->temp_max_today = $next24Hours->filter(fn ($row) => substr($row->valid_at, 0, 10) === now()->toDateString())->max('temperature_c');
        $adviceWeather->temp_min_today = $next24Hours->filter(fn ($row) => substr($row->valid_at, 0, 10) === now()->toDateString())->min('temperature_c');
    }

    return response()->json([
        'wilayah' => $region,
        'cuaca_saat_ini' => $current,
        'prakiraan_7_hari' => $forecast,
        'kondisi_laut_kota' => $region->type === 'city' ? [
            'saat_ini' => $marineCurrent,
            'prakiraan_7_hari' => $marineForecast,
            'catatan' => 'Informasi laut bersifat umum dan bukan untuk keputusan keselamatan navigasi.',
        ] : null,
        'saran_aktivitas' => $activityAdvice->forWeather($adviceWeather, $marineCurrent),
        'diperbarui_pada' => $current?->fetched_at,
        'ditarik_pada' => $latestFetchedAt,
        'iklim' => $climateInsight->forRegion($code),
    ]);
})->where('code', '[0-9.]+');

Route::get('/api/climate/regions/{code}', function (string $code, ClimateInsight $climateInsight) {
    abort_unless(DB::table('regions')->where('code', $code)->where('active', true)->exists(), 404);

    return response()->json($climateInsight->forRegion($code));
})->where('code', '[0-9.]+');

Route::get('/api/climate/summary/{code}', function (string $code, ClimateInsight $climateInsight) {
    abort_unless(DB::table('regions')->where('code', $code)->where('active', true)->exists(), 404);

    return response()->json($climateInsight->forRegion($code));
})->where('code', '[0-9.]+');

Route::post('/api/weather/refresh', function () {
    $exitCode = Artisan::call('weather:sync');

    return response()->json([
        'success' => $exitCode === 0,
        'message' => $exitCode === 0 ? 'Data cuaca berhasil diperbarui.' : 'Pembaruan gagal. Data sebelumnya tetap ditampilkan.',
    ], $exitCode === 0 ? 200 : 502);
})->middleware('throttle:3,1');
