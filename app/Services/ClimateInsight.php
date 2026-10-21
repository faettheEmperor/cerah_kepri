<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClimateInsight
{
    public function forRegion(string $code): array
    {
        $region = DB::table('regions')->where('code', $code)->first();
        if ($region && $region->type !== 'village') {
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
                        'pola_bulan_ini' => 'Rata-rata ringkasan kelurahan di wilayah ini',
                        'normal_hujan_bulan_ini_mm' => $summaries->filter(fn ($item) => $item['normal_hujan_bulan_ini_mm'] !== null)->avg('normal_hujan_bulan_ini_mm'),
                        'rentang_normal_tahun' => 'per kelurahan',
                        'sumber' => 'Rata-rata titik estimasi Open-Meteo ERA5-Land pada kelurahan yang memiliki data.',
                        'data_sampai' => $summaries->min('data_sampai'),
                        'dianalisis_sampai' => $summaries->min('dianalisis_sampai'),
                        'seri_30_hari' => [],
                    ];
                }
            }
            return ['tersedia' => false, 'pesan' => 'Data historis kelurahan belum disinkronkan.'];
        }
        $today = Carbon::today('Asia/Jakarta');
        $latestDate = DB::table('climate_daily_summaries')->where('region_code', $code)->max('observed_on');
        if (! $latestDate) {
            return ['tersedia' => false, 'pesan' => 'Data historis iklim belum disinkronkan.'];
        }

        $latest = Carbon::parse($latestDate, 'Asia/Jakarta');
        $recent = DB::table('climate_daily_summaries')
            ->where('region_code', $code)->whereBetween('observed_on', [$today->copy()->subDays(29)->toDateString(), $latestDate])
            ->orderByDesc('observed_on')->get();
        $monthStart = $today->copy()->startOfMonth();
        $climatology = DB::table('climate_daily_summaries')
            ->where('region_code', $code)
            ->whereMonth('observed_on', Carbon::parse($latestDate, 'Asia/Jakarta')->month)
            ->whereBetween('observed_on', [$today->copy()->subYears(10)->toDateString(), $latestDate])
            ->selectRaw('EXTRACT(YEAR FROM observed_on) AS year, SUM(precipitation_mm) AS rain_mm, AVG(temperature_mean_c) AS temperature_c')
            ->groupByRaw('EXTRACT(YEAR FROM observed_on)')->get();

        $asOf = min($latestDate, $today->toDateString());
        $lastObservedRainyDay = DB::table('climate_daily_summaries')
            ->where('region_code', $code)->where('observed_on', '<=', $asOf)
            ->where('precipitation_mm', '>=', 1)->max('observed_on');
        $dryDays = $lastObservedRainyDay
            ? Carbon::parse($lastObservedRainyDay, 'Asia/Jakarta')->diffInDays(Carbon::parse($asOf, 'Asia/Jakarta'))
            : null;

        $completeRecent = $recent->filter(fn ($row) => $row->precipitation_mm !== null);
        $currentRain = $completeRecent->sum(fn ($row) => (float) $row->precipitation_mm);
        $wetDays = $completeRecent->filter(fn ($row) => (float) $row->precipitation_mm >= 1)->count();
        $meanTemp = $recent->filter(fn ($row) => $row->temperature_mean_c !== null)->avg('temperature_mean_c');
        $monthlyAverages = $climatology->filter(fn ($row) => $row->rain_mm !== null)->values();
        $normalRain = $monthlyAverages->avg('rain_mm');
        $expectedRain = $normalRain > 0 ? $normalRain * $completeRecent->count() / Carbon::parse($latestDate)->daysInMonth : null;
        $rainRatio = $expectedRain > 0 ? $currentRain / $expectedRain : null;
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
            'sumber' => 'Open-Meteo ERA5-Land · estimasi reanalisis titik wilayah',
            'hari_tanpa_hujan' => $dryDays,
            'estimasi_hth' => true,
            'definisi_hth' => 'Beruntun sejak hujan harian ≥ 1 mm; data historis umumnya tertinggal sekitar 5 hari.',
            'hujan_30_hari_mm' => round($currentRain, 1),
            'hari_basah_30_hari' => $wetDays,
            'suhu_rata_rata_30_hari_c' => $meanTemp === null ? null : round((float) $meanTemp, 1),
            'pola_bulan_ini' => $monthlyLabel,
            'indeks_hujan_vs_normal' => $rainRatio === null ? null : round($rainRatio * 100),
            'normal_hujan_bulan_ini_mm' => $normalRain === null ? null : round((float) $normalRain, 1),
            'hari_tercakup_30_hari' => $completeRecent->count(),
            'harapan_hujan_terkoreksi_mm' => $expectedRain === null ? null : round((float) $expectedRain, 1),
            'rentang_normal_tahun' => $monthlyAverages->count() ? $monthlyAverages->min('year').'-'.$monthlyAverages->max('year') : null,
            'seri_30_hari' => $recent->reverse()->map(fn ($row) => [
                'tanggal' => $row->observed_on,
                'hujan_mm' => $row->precipitation_mm === null ? null : (float) $row->precipitation_mm,
                'suhu_rata_rata_c' => $row->temperature_mean_c === null ? null : (float) $row->temperature_mean_c,
            ])->values(),
        ];
    }
}
