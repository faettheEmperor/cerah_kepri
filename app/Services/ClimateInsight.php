<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClimateInsight
{
    public function forRegion(string $code, int $chartDays = 30): array
    {
        $region = DB::table('regions')->where('code', $code)->first();
        if ($region && $region->type !== 'village'
            && ! DB::table('climate_daily_summaries')->where('region_code', $code)->exists()) {
            $children = DB::table('regions')->where('active', true)->where('parent_code', $code)->get();
            if ($children->isNotEmpty()) {
                $summaries = $children->flatMap(function ($child) {
                    $item = $this->forRegion($child->code);
                    if (! ($item['tersedia'] ?? false)) return [];
                    return ! empty($item['wilayah_agregat']) && isset($item['ringkasan_kelurahan'])
                        ? $item['ringkasan_kelurahan']
                        : [$item];
                });
                if ($summaries->isNotEmpty()) {
                    $rainNormals = $summaries->filter(fn ($item) => ($item['normal_hujan_bulan_ini_mm'] ?? null) !== null);
                    $temperatureNormals = $summaries->filter(fn ($item) => ($item['suhu_normal_bulan_ini_c'] ?? null) !== null);
                    $humidityNormals = $summaries->filter(fn ($item) => ($item['normal_kelembapan_bulan_ini'] ?? null) !== null);
                    return [
                        'tersedia' => true,
                        'wilayah_agregat' => true,
                        'jumlah_kelurahan' => $summaries->count(),
                        'wilayah_cakupan' => $children->count(),
                        'hari_tanpa_hujan' => null,
                        'estimasi_hth' => false,
                        'definisi_hth' => 'Nilai HTH hanya tersedia per kelurahan; ringkasan kota/kecamatan tidak digabung menjadi satu deret.',
                        'hujan_30_hari_mm' => round($summaries->avg('hujan_30_hari_mm'), 1),
                        'hari_basah_30_hari' => (int) round($summaries->avg('hari_basah_30_hari')),
                        'suhu_rata_rata_30_hari_c' => $summaries->filter(fn ($item) => $item['suhu_rata_rata_30_hari_c'] !== null)->avg('suhu_rata_rata_30_hari_c'),
                        'suhu_normal_bulan_ini_c' => $temperatureNormals->avg('suhu_normal_bulan_ini_c'),
                        'kelembapan_rata_rata_30_hari' => $summaries->filter(fn ($item) => ($item['kelembapan_rata_rata_30_hari'] ?? null) !== null)->avg('kelembapan_rata_rata_30_hari'),
                        'normal_kelembapan_bulan_ini' => $humidityNormals->avg('normal_kelembapan_bulan_ini'),
                        'pola_bulan_ini' => 'Rata-rata ringkasan kelurahan di wilayah ini',
                        'normal_hujan_bulan_ini_mm' => $rainNormals->avg('normal_hujan_bulan_ini_mm'),
                        'hujan_vs_normal_persen' => $summaries->filter(fn ($item) => ($item['hujan_vs_normal_persen'] ?? null) !== null)->avg('hujan_vs_normal_persen'),
                        'harapan_hujan_terkoreksi_mm' => $summaries->filter(fn ($item) => ($item['harapan_hujan_terkoreksi_mm'] ?? null) !== null)->avg('harapan_hujan_terkoreksi_mm'),
                        'rentang_normal_tahun' => 'per kelurahan',
                        'sumber' => 'Rata-rata estimasi titik model Open-Meteo (ERA5 dan Forecast API) pada kelurahan yang memiliki data.',
                        'data_sampai' => $summaries->min('data_sampai'),
                        'dianalisis_sampai' => $summaries->min('dianalisis_sampai'),
                        'seri_30_hari' => [],
                    ];
                }
            }
            return ['tersedia' => false, 'pesan' => 'Data historis kelurahan belum disinkronkan.'];
        }
        $today = Carbon::today('Asia/Jakarta');
        // Periode 30 hari kalender yang sudah SELESAI, tidak termasuk hari ini.
        $windowEnd = $today->copy()->subDay();
        $windowStart = $windowEnd->copy()->subDays(29);
        $latestDate = DB::table('climate_daily_summaries')->where('region_code', $code)->max('observed_on');
        if (! $latestDate) {
            return ['tersedia' => false, 'pesan' => 'Data historis iklim belum disinkronkan.'];
        }

        // HTH dan ringkasan 30 hari hanya menggunakan hari yang sudah selesai.
        // Baris hari ini bersumber dari prakiraan penuh hari, bukan realisasi final.
        $asOf = min($latestDate, $windowEnd->toDateString());
        $latest = Carbon::parse($asOf, 'Asia/Jakarta');
        $chartStart = $today->copy()->subDays(max(1, $chartDays) - 1)->toDateString();
        $chartSeries = DB::table('climate_daily_summaries')
            ->where('region_code', $code)
            ->whereBetween('observed_on', [$chartStart, $latestDate])
            ->orderBy('observed_on')
            ->get();
        $recent = DB::table('climate_daily_summaries')
            ->where('region_code', $code)->whereBetween('observed_on', [$windowStart->toDateString(), $windowEnd->toDateString()])
            ->orderByDesc('observed_on')->get();
        // Latest ERA5 date may be in the previous calendar month.
        $monthStart = $latest->copy()->startOfMonth();
        $climatology = DB::table('climate_daily_summaries')
            ->where('region_code', $code)
            ->whereMonth('observed_on', $latest->month)
            ->whereBetween('observed_on', [$today->copy()->subYears(10)->toDateString(), $latestDate])
            ->selectRaw('EXTRACT(YEAR FROM observed_on) AS year, SUM(precipitation_mm) AS rain_mm, AVG(temperature_mean_c) AS temperature_c, AVG(relative_humidity_mean) AS humidity')
            ->groupByRaw('EXTRACT(YEAR FROM observed_on)')->get();

        // Jumlah HTH selama 30 hari terakhir (tidak harus berturut-turut).
        // Hanya hari dengan curah hujan TERISI dan < 1 mm yang dihitung.
        // Hari ini dan tanggal tanpa data sama sekali tidak ikut dihitung.
        $completeRecent = $recent
            ->filter(fn ($row) => $row->precipitation_mm !== null)
            ->unique('observed_on')
            ->values();
        $dryDays = $completeRecent->isEmpty()
            ? null
            : $completeRecent->filter(fn ($row) => (float) $row->precipitation_mm < 1)->count();
        $dryDaysLowerBound = false; // Kompatibilitas dengan konsumen API lama.

        $currentRain = $completeRecent->sum(fn ($row) => (float) $row->precipitation_mm);
        $wetDays = $completeRecent->filter(fn ($row) => (float) $row->precipitation_mm >= 1)->count();
        $meanTemp = $recent->filter(fn ($row) => $row->temperature_mean_c !== null)->avg('temperature_mean_c');
        $meanHumidity = $recent->filter(fn ($row) => $row->relative_humidity_mean !== null)->avg('relative_humidity_mean');
        $monthlyAverages = $climatology->filter(fn ($row) => $row->rain_mm !== null)->values();
        $normalRain = $monthlyAverages->avg('rain_mm');
        $normalTemp = $monthlyAverages->filter(fn ($row) => $row->temperature_c !== null)->avg('temperature_c');
        $normalHumidity = $monthlyAverages->filter(fn ($row) => $row->humidity !== null)->avg('humidity');
        $expectedRain = $normalRain > 0 ? $normalRain * $completeRecent->count() / Carbon::parse($latestDate)->daysInMonth : null;
        $rainRatio = $expectedRain > 0 ? $currentRain / $expectedRain : null;
        // Rekor terpanjang beruntun tetap statistik terpisah dari TOTAL HTH 30 hari.
        // Bulan REKOR wajib mengikuti bulan kalender saat ini (WIB), bukan bulan
        // terakhir di dataset. Tidak ada data pada bulan ini = null, bukan 0.
        $recordMonthStart = $today->copy()->startOfMonth();
        $recordEnd = $windowEnd->copy();
        if ($recordEnd->gt(Carbon::parse($asOf, 'Asia/Jakarta'))) {
            $recordEnd = Carbon::parse($asOf, 'Asia/Jakarta');
        }
        $longestDryRun = null;
        if ($recordEnd->gte($recordMonthStart)) {
            $monthRows = DB::table('climate_daily_summaries')
                ->where('region_code', $code)
                ->whereBetween('observed_on', [
                    $recordMonthStart->toDateString(), $recordEnd->toDateString()
                ])
                ->orderBy('observed_on')
                ->get(['observed_on', 'precipitation_mm'])
                ->keyBy('observed_on');

            $hasMonthlyRainData = $monthRows->contains(
                fn ($row) => $row->precipitation_mm !== null
            );
            if ($hasMonthlyRainData) {
                $longestDryRun = 0;
                $dryRun = 0;
                for (
                    $date = $recordMonthStart->copy();
                    $date->lte($recordEnd);
                    $date->addDay()
                ) {
                    $row = $monthRows->get($date->toDateString());
                    if ($row !== null && $row->precipitation_mm !== null
                        && (float) $row->precipitation_mm < 1) {
                        $dryRun++;
                        $longestDryRun = max($longestDryRun, $dryRun);
                    } else {
                        $dryRun = 0;
                    }
                }
            }
        }
        $monthlyLabel = match (true) {
            $rainRatio === null => 'Belum cukup data untuk membandingkan pola hujan',
            $rainRatio < 0.4 => 'Jauh lebih kering dari pola historis bulan ini',
            $rainRatio < 0.75 => 'Cenderung lebih kering dari pola historis bulan ini',
            $rainRatio <= 1.25 => 'Mendekati pola historis bulan ini',
            default => 'Lebih basah dari pola historis bulan ini',
        };

        return [
            'tersedia' => true,
            'data_sampai' => $latestDate,
            'dianalisis_sampai' => $asOf,
            'hari_ini_prakiraan' => $latestDate === $today->toDateString(),
            'sumber' => 'Open-Meteo ERA5 untuk historis; Forecast API untuk hari terbaru. Hari ini berupa prakiraan, bukan data harian final.',
            'hari_tanpa_hujan' => $dryDays,
            'hth_periode_mulai' => $windowStart->toDateString(),
            'hth_periode_sampai' => $windowEnd->toDateString(),
            'bulan_rekor_hth' => $recordMonthStart->toDateString(),
            'hth_batas_bawah' => $dryDaysLowerBound,
            'estimasi_hth' => true,
            'definisi_hth' => 'HTH adalah jumlah seluruh hari dengan curah hujan < 1 mm dalam 30 hari kalender terakhir yang sudah selesai (tidak harus berurutan). Hari berjalan tidak dihitung; hari tanpa data tidak dianggap kering. Semua nilai merupakan estimasi model, bukan pengukuran langsung.',
            'hujan_30_hari_mm' => round($currentRain, 1),
            'hari_basah_30_hari' => $wetDays,
            'suhu_rata_rata_30_hari_c' => $meanTemp === null ? null : round((float) $meanTemp, 1),
            'suhu_normal_bulan_ini_c' => $normalTemp === null ? null : round((float) $normalTemp, 1),
            'kelembapan_rata_rata_30_hari' => $meanHumidity === null ? null : round((float) $meanHumidity),
            'normal_kelembapan_bulan_ini' => $normalHumidity === null ? null : round((float) $normalHumidity),
            'hari_kering_terpanjang_bulan_ini' => $longestDryRun,
            'pola_bulan_ini' => $monthlyLabel,
            'indeks_hujan_vs_normal' => $rainRatio === null ? null : round($rainRatio * 100),
            'hujan_vs_normal_persen' => $rainRatio === null ? null : (int) round(($rainRatio - 1) * 100),
            'normal_hujan_bulan_ini_mm' => $normalRain === null ? null : round((float) $normalRain, 1),
            'hari_tercakup_30_hari' => $completeRecent->count(),
            'harapan_hujan_terkoreksi_mm' => $expectedRain === null ? null : round((float) $expectedRain, 1),
            'rentang_normal_tahun' => $monthlyAverages->count() ? $monthlyAverages->min('year').'-'.$monthlyAverages->max('year') : null,
            'seri_30_hari' => $recent->reverse()->map(fn ($row) => [
                'tanggal' => $row->observed_on,
                'hujan_mm' => $row->precipitation_mm === null ? null : (float) $row->precipitation_mm,
                'suhu_rata_rata_c' => $row->temperature_mean_c === null ? null : (float) $row->temperature_mean_c,
            ])->values(),
            'seri_historis' => $chartSeries->map(fn ($row) => [
                'tanggal' => $row->observed_on,
                'hujan_mm' => $row->precipitation_mm === null ? null : (float) $row->precipitation_mm,
                'suhu_rata_rata_c' => $row->temperature_mean_c === null ? null : (float) $row->temperature_mean_c,
                'us_aqi_rata_rata' => $row->us_aqi_mean === null ? null : (float) $row->us_aqi_mean,
                'pm2_5_rata_rata' => $row->pm2_5_mean_ug_m3 === null ? null : (float) $row->pm2_5_mean_ug_m3,
                'kelembapan_rata_rata' => $row->relative_humidity_mean === null ? null : (float) $row->relative_humidity_mean,
            ])->values(),
        ];
    }
}
