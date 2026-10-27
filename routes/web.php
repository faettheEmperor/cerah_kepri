<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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

Route::get('/api/news', function () {
    $articles = Cache::remember('news:latest', now()->addMinutes(5), fn () =>
        DB::table('news_articles')->orderByDesc('published_at')->limit((int) config('news.display_limit', 12))
            ->get(['source_name', 'title', 'summary', 'url', 'published_at', 'matched_keywords'])
            ->map(fn ($article) => [
                'source' => $article->source_name,
                'title' => $article->title,
                'summary' => $article->summary,
                'url' => $article->url,
                'published_at' => $article->published_at,
                'keywords' => json_decode($article->matched_keywords, true) ?: [],
            ])
            ->values()
    );

    return response()->json(['articles' => $articles]);
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
        ->select('r.code', 'r.name', 'r.parent_code', 'r.latitude', 'r.longitude', 'w.temperature_c', 'w.relative_humidity', 'w.visibility_m', 'w.weather_code', 'w.is_day', 'w.valid_at', 'w.rain_mm', 'w.wind_speed_kmh', 'w.pm2_5_ug_m3', 'w.us_aqi', 'forecast.precipitation_probability')
        ->orderBy('r.code')->get();

    return response()->json($points);
});

Route::get('/api/weather/boundaries/{level}/{code}', function (string $level, string $code, \Illuminate\Http\Request $request) {
    abort_unless(in_array($level, ['municipalities', 'districts', 'villages'], true), 404);
    abort_unless(match ($level) {
        'municipalities' => preg_match('/^21$/', $code) === 1,
        'districts' => preg_match('/^21\.\d{2}$/', $code) === 1,
        'villages' => preg_match('/^21\.\d{2}\.\d{2}$/', $code) === 1,
        default => false,
    }, 404);

    // BIG layers: 0 kabupaten/kota, 1 kecamatan, 2 kelurahan/desa.
    $layer = match ($level) {'municipalities' => 0, 'districts' => 1, 'villages' => 2};
    $url = "https://geoservices.big.go.id/gis/rest/services/BAPANAS/Batas_Administrasi/MapServer/{$layer}/query";
    $cacheKey = "weather-boundaries:v4:{$level}:{$code}";
    if (! $request->boolean('refresh') && ($cached = Cache::get($cacheKey)) !== null) {
        return response()->json($cached)->header('Cache-Control', 'public, max-age=86400');
    }
    $digits = preg_replace('/\D/', '', $code);
    // BIG is not consistent about dotted PUM codes, so match dotted and compact forms.
    $where = match ($level) {
        'municipalities' => "(KDPBPS = '{$code}' OR KDPPUM = '{$code}' OR KDPKAB LIKE '{$code}.%' OR KDPKAB LIKE '{$digits}%')",
        'districts' => "(KDPKAB = '{$code}' OR KDPKAB = '{$digits}' OR KDCPUM LIKE '{$code}.%' OR KDCPUM LIKE '{$digits}%')",
        default => "(KDCPUM = '{$code}' OR KDCPUM = '{$digits}' OR KDCPUM LIKE '{$code}.%' OR KDCPUM LIKE '{$digits}%')",
    };
    $query = [
        'where' => $where,
        'outFields' => 'OBJECTID,KDBBPS,KDCBPS,KDCPUM,KDEBPS,KDEPUM,KDPKAB,KDPPUM,WADMKC,WADMKD,WADMKK,WADMPR',
        'returnGeometry' => 'true',
        'returnZ' => 'false',
        'returnM' => 'false',
        'outSR' => '4326',
        'maxAllowableOffset' => $level === 'municipalities' ? 0.002 : ($level === 'districts' ? 0.0015 : 0.0002),
        'geometryPrecision' => 4,
        'resultRecordCount' => 100,
        'f' => 'geojson',
    ];

    $geoJson = null;
    $lastError = null;
    try {
        $response = Http::acceptJson()->connectTimeout(8)->timeout(25)->get($url, $query);
        if (! $response->successful()) {
            $lastError = 'BIG menolak permintaan batas wilayah (HTTP '.$response->status().').';
        } else {
            $candidate = $response->json();
            if (($candidate['type'] ?? null) !== 'FeatureCollection' || ! is_array($candidate['features'] ?? null)) {
                $lastError = data_get($candidate, 'error.message') ?? 'BIG mengembalikan format data batas yang tidak dikenali.';
            } else {
                $candidate['features'] = array_values(array_filter($candidate['features'], fn ($feature) =>
                    in_array($feature['geometry']['type'] ?? null, ['Polygon', 'MultiPolygon'], true)
                ));
                if (! empty($candidate['features'])) {
                    $geoJson = $candidate;
                    Cache::put($cacheKey, $geoJson, now()->addDay());
                } else {
                    $lastError = 'BIG belum mengembalikan batas wilayah untuk kode ini.';
                }
            }
        }
    } catch (\Throwable $exception) {
        report($exception);
        $lastError = 'BIG tidak dapat dijangkau untuk memuat batas wilayah.';
    }

    if (! $geoJson) {
        return response()->json(['message' => $lastError ?? 'BIG belum mengembalikan batas wilayah untuk kode ini.'], 502)
            ->header('Cache-Control', 'no-store');
    }

    return response()->json($geoJson)->header('Cache-Control', 'public, max-age=86400');
})->where('code', '[0-9.]+');

Route::get('/api/weather/regions/{code}', function (string $code, ActivityAdvice $activityAdvice) {
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
        if ($currentForecast) {
            $current->precipitation_probability = $currentForecast->precipitation_probability;
            if ($current->us_aqi === null || $current->us_aqi === '') {
                $current->us_aqi = $currentForecast->us_aqi;
            }
            if ($current->pm2_5_ug_m3 === null || $current->pm2_5_ug_m3 === '') {
                $current->pm2_5_ug_m3 = $currentForecast->pm2_5_ug_m3;
            }
            if ($current->us_aqi === null || $current->us_aqi === '') {
                $current->us_aqi = $currentForecast->us_aqi;
            }
        }
    }
    $forecastRun = DB::table('weather_observations')->where('region_code', $code)->where('kind', 'forecast')->max('fetched_at');
    $forecast = $forecastRun
        ? DB::table('weather_observations')->where('region_code', $code)->where('kind', 'forecast')->where('fetched_at', $forecastRun)->orderBy('valid_at')->get()
        : collect();
    $historyToday = now('Asia/Jakarta')->startOfDay();
    $historyDays = $region->type === 'village' ? 30 : 7;
    $historyEnd = $historyToday->copy()->subDay()->toDateString();
    $historyStart = $historyToday->copy()->subDays($historyDays)->toDateString();
    $weatherHistory = collect();
    if (in_array($region->type, ['district', 'village'], true)) {
        $directHistory = DB::table('climate_daily_summaries')
            ->where('region_code', $code)
            ->whereBetween('observed_on', [$historyStart, $historyEnd])
            ->get()->keyBy('observed_on');

        if ($region->type === 'district') {
            $villageCodes = DB::table('regions')->where('parent_code', $code)->where('type', 'village')->where('active', true)->pluck('code');
            $villageHistory = $villageCodes->isEmpty()
                ? collect()
                : DB::table('climate_daily_summaries')->whereIn('region_code', $villageCodes)
                    ->whereBetween('observed_on', [$historyStart, $historyEnd])->get()->groupBy('observed_on');

            for ($daysAgo = 7; $daysAgo >= 1; $daysAgo--) {
                $date = $historyToday->copy()->subDays($daysAgo)->toDateString();
                $villageRows = $villageHistory->get($date, collect());
                $localSummary = null;
                if ($villageRows->isNotEmpty()) {
                    $temperaturesMax = $villageRows->pluck('temperature_max_c')->filter(fn ($value) => $value !== null)->map(fn ($value) => (float) $value);
                    $temperaturesMin = $villageRows->pluck('temperature_min_c')->filter(fn ($value) => $value !== null)->map(fn ($value) => (float) $value);
                    $rainValues = $villageRows->pluck('precipitation_mm')->filter(fn ($value) => $value !== null)->map(fn ($value) => (float) $value);
                    $weatherCodes = $villageRows->pluck('weather_code')->filter(fn ($value) => $value !== null)->countBy()->sortDesc();
                    $localSummary = (object) [
                        'observed_on' => $date,
                        'temperature_max_c' => $temperaturesMax->isNotEmpty() ? $temperaturesMax->max() : null,
                        'temperature_min_c' => $temperaturesMin->isNotEmpty() ? $temperaturesMin->min() : null,
                        // Rain is a depth; average across villages instead of summing spatially.
                        'precipitation_mm' => $rainValues->count() >= (int) ceil($villageCodes->count() * 0.75) && $rainValues->isNotEmpty() ? $rainValues->avg() : null,
                        'weather_code' => $weatherCodes->isNotEmpty() ? (int) $weatherCodes->keys()->first() : null,
                        'source' => 'open_meteo_current_village_aggregate',
                    ];
                }

                $historical = $directHistory->get($date);
                if ($historical && $historical->source === 'open_meteo_era5') {
                    $weatherHistory->push((object) [
                        'observed_on' => $date,
                        'temperature_max_c' => $historical->temperature_max_c ?? $localSummary?->temperature_max_c,
                        'temperature_min_c' => $historical->temperature_min_c ?? $localSummary?->temperature_min_c,
                        'precipitation_mm' => $historical->precipitation_mm ?? $localSummary?->precipitation_mm,
                        'weather_code' => $historical->weather_code ?? $localSummary?->weather_code,
                        'source' => 'open_meteo_era5',
                    ]);
                } elseif ($localSummary) {
                    $weatherHistory->push($localSummary);
                } elseif ($historical) {
                    $weatherHistory->push($historical);
                }
            }
        } else {
            $weatherHistory = $directHistory->sortBy('observed_on')->values();
        }
    }
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
        'riwayat_7_hari' => $region->type === 'district' ? $weatherHistory : collect(),
        'riwayat_30_hari' => $region->type === 'village' ? $weatherHistory : collect(),
        'saran_aktivitas' => $activityAdvice->forWeather($adviceWeather),
        'diperbarui_pada' => $current?->fetched_at,
        'ditarik_pada' => $latestFetchedAt,
    ]);
})->where('code', '[0-9.]+');

Route::get('/api/climate/regions/{code}', function (string $code, \Illuminate\Http\Request $request, ClimateInsight $climateInsight) {
    abort_unless(DB::table('regions')->where('code', $code)->where('active', true)->exists(), 404);
    $days = (int) $request->query('days', 30);
    abort_unless(in_array($days, [7, 30, 90, 365], true), 422);

    $summary = Cache::remember("climate-summary:v1:{$code}:{$days}", now()->addMinutes(15), fn () => $climateInsight->forRegion($code, $days));

    return response()->json($summary);
})->where('code', '[0-9.]+');

Route::get('/api/climate/summary/{code}', function (string $code, \Illuminate\Http\Request $request, ClimateInsight $climateInsight) {
    abort_unless(DB::table('regions')->where('code', $code)->where('active', true)->exists(), 404);
    $days = (int) $request->query('days', 30);
    abort_unless(in_array($days, [7, 30, 90, 365], true), 422);

    $summary = Cache::remember("climate-summary:v1:{$code}:{$days}", now()->addMinutes(15), fn () => $climateInsight->forRegion($code, $days));

    return response()->json($summary);
})->where('code', '[0-9.]+');

Route::post('/api/weather/refresh', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'region_code' => ['required', 'string', 'max:20'],
    ]);
    $region = DB::table('regions')->where('code', $validated['region_code'])->where('active', true)->first();
    if (! $region) {
        return response()->json(['success' => false, 'message' => 'Pilih wilayah aktif yang ingin diperbarui.'], 422);
    }

    $exitCode = Artisan::call('weather:sync', ['--region' => $region->code]);
    $commandOutput = trim(Artisan::output());

    return response()->json([
        'success' => $exitCode === 0,
        'message' => $exitCode === 0
            ? "Data cuaca {$region->name} berhasil diperbarui."
            : ($commandOutput !== '' ? $commandOutput : 'Pembaruan gagal. Data sebelumnya tetap ditampilkan.'),
    ], $exitCode === 0 ? 200 : 502);
})->middleware('throttle:3,1');
