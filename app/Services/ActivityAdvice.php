<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class ActivityAdvice
{
    public function forWeather(?object $weather, ?object $marine = null): array
    {
        if (! $weather) {
            return [];
        }

        $chance = (int) ($weather->rain_chance_24h ?? $weather->precipitation_probability ?? 0);
        $eveningChance = (int) ($weather->evening_rain_probability ?? $chance);
        $nightChance = (int) ($weather->night_rain_probability ?? $chance);
        $rainNow = $this->value($weather, 'rain_mm');
        $maxHourlyRain = $this->value($weather, 'max_rain_hourly_mm', $rainNow ?? 0);
        $rainTotal = $this->value($weather, 'rain_total_24h_mm', $rainNow ?? 0);
        $wind = $this->value($weather, 'max_wind_24h_kmh', $this->value($weather, 'wind_speed_kmh', 0));
        $windDirection = $this->direction($this->value($weather, 'max_wind_direction_deg', $this->value($weather, 'wind_direction_deg')));
        $visibility = $this->value($weather, 'min_visibility_24h_m', $this->value($weather, 'visibility_m'));
        $humidity = $this->value($weather, 'max_humidity_24h', $this->value($weather, 'relative_humidity'));
        $feels = $this->value($weather, 'apparent_temperature_c');
        $uv = $this->value($weather, 'uv_index');
        $pm25 = $this->value($weather, 'max_pm2_5_24h', $this->value($weather, 'pm2_5_ug_m3'));
        $storm = (bool) ($weather->storm_expected_24h ?? in_array((int) ($weather->weather_code ?? 0), [95, 96, 99], true));
        $wave = $this->value($marine, 'wave_height_m');
        $peakEvening = $this->hour($weather->evening_rain_at ?? $weather->peak_rain_at ?? null);
        $peakNight = $this->hour($weather->night_rain_at ?? null);
        $heavyRain = $maxHourlyRain > 20;
        $windKnots = $wind / 1.852;
        $coastalCode = (string) ($weather->region_code ?? '');
        $coastalCodes = ['21.72.03.1001', '21.72.03.1003', '21.72.03.1004', '21.72.03.1005', '21.72.04.1004', '21.72.04.1005'];
        $isCoastal = ($weather->region_type ?? null) === 'city' || in_array($coastalCode, $coastalCodes, true);
        $rainReason = $this->rainReason($weather, $chance, $peakEvening);

        $advice = [];

        if ($isCoastal) {
            $crossingRisk = $wind >= 37 || ($wave !== null && $wave >= 1.5) || ($visibility !== null && $visibility < 1000) || $heavyRain || $storm;
            $crossingReason = $this->joinReasons([
                'angin '.number_format($wind, 1, ',', '.').' km/jam ('.number_format($windKnots, 1, ',', '.').' knot)',
                $windDirection === null ? null : 'arah angin '.$windDirection,
                $wave === null ? null : 'gelombang '.number_format($wave, 1, ',', '.').' m',
                $visibility === null ? null : 'jarak pandang '.number_format($visibility / 1000, 1, ',', '.').' km',
                'hujan per jam maks. '.number_format($maxHourlyRain, 1, ',', '.').' mm',
                $storm ? 'potensi badai petir' : null,
            ]);
            $advice['penyeberangan_pompong'] = $this->item(
                $crossingRisk ? 'waspada' : 'informasi',
                $crossingRisk
                    ? 'Penyeberangan Tanjungpinang–Penyengat/Kampung Bugis dapat perlu berteduh. Tunda bila operator menghentikan layanan dan pastikan kondisi langsung sebelum berangkat.'
                    : 'Belum ada pemicu ambang pada prakiraan titik ini. Tetap konfirmasi jadwal dan kondisi laut kepada operator pompong.',
                $crossingReason.' · Data laut ini indikasi umum, bukan izin berlayar.'
            );

            $fishingRisk = $wind >= 37 || $storm;
            $advice['nelayan_kelong'] = $this->item(
                $fishingRisk ? 'waspada' : 'saran',
                $fishingRisk
                    ? 'Pertimbangkan tidak melaut atau kembali lebih awal. Cuaca buruk dapat mengurangi tangkapan dan pasokan ikan ke Pasar Bintan Centre serta Pasar Kota Lama.'
                    : 'Periksa radar dan langit sebelum menebar jaring; squall lokal bisa berkembang cepat dan tidak selalu tertangkap prakiraan.',
                $this->joinReasons(['angin maks. '.number_format($wind, 1, ',', '.').' km/jam', $windDirection === null ? null : 'arah '.$windDirection, $storm ? 'kode cuaca badai petir terdeteksi' : 'tidak ada kode badai petir pada prakiraan'])
            );

            $dryingRisk = $chance >= 50 || $maxHourlyRain >= 1 || ($humidity !== null && $humidity >= 80);
            $advice['penjemuran_hasil_laut'] = $this->item(
                $dryingRisk ? 'waspada' : 'baik',
                $dryingRisk ? 'Gunakan tempat pengering beratap dan lindungi ikan atau sotong agar mutu hasil usaha pesisir tidak turun.' : 'Peluang hujan relatif rendah; tetap pantau perubahan cuaca siang dan sore.',
                $this->joinReasons(['peluang hujan maks. '.$chance.'%', 'kelembapan maks. '.($humidity === null ? '—' : number_format($humidity, 0, ',', '.').'%'), 'hujan per jam maks. '.number_format($maxHourlyRain, 1, ',', '.').' mm'])
            );
        }

        $airportRisk = ($visibility !== null && $visibility < 1500) || $storm || $heavyRain || $wind >= 37;
        $advice['penerbangan_rhf'] = $this->item(
            $airportRisk ? 'waspada' : 'informasi',
            $airportRisk
                ? 'Cuaca dapat mengganggu operasi penerbangan. Pantau informasi maskapai dan bandara; prakiraan ini bukan METAR/TAF.'
                : 'Tidak ada pemicu ambang utama pada titik prakiraan ini. Jadwal tetap perlu dikonfirmasi ke maskapai atau bandara RHF.',
            $this->joinReasons([
                $visibility === null ? 'jarak pandang tidak tersedia' : 'jarak pandang min. '.number_format($visibility / 1000, 1, ',', '.').' km',
                'hujan per jam maks. '.number_format($maxHourlyRain, 1, ',', '.').' mm',
                'angin maks. '.number_format($wind, 1, ',', '.').' km/jam',
                $storm ? 'potensi badai petir' : null,
            ])
        );

        $commuteRisk = $eveningChance >= 50 || $heavyRain || $storm;
        $advice['komuter_roda_dua'] = $this->item(
            $commuteRisk ? 'waspada' : 'baik',
            $commuteRisk ? 'Siapkan jas hujan, hindari berteduh di badan jalan, dan waspadai genangan di ruas cekung sekitar Batu IX/Melayu Kota Piring saat hujan lebat.' : 'Prakiraan belum menunjukkan pemicu hujan sore yang kuat; tetap cek pembaruan sebelum pulang.',
            $this->joinReasons(['peluang hujan sore maks. '.$eveningChance.'%'.($peakEvening ? ' sekitar '.$peakEvening : ''), 'hujan per jam maks. '.number_format($maxHourlyRain, 1, ',', '.').' mm', $storm ? 'potensi badai petir' : null])
        );

        if ($isCoastal) {
            $culinaryRisk = $nightChance >= 50 || $wind >= 37 || $heavyRain;
            $advice['kuliner_tepi_laut'] = $this->item(
                $culinaryRisk ? 'waspada' : 'baik',
                $culinaryRisk ? 'Pedagang tenda dan pengunjung di Kota Lama/Melayu Square sebaiknya menyiapkan tempat berteduh atau rencana tutup lebih awal.' : 'Cuaca malam diprakirakan relatif mendukung kegiatan luar ruang; siapkan perlindungan bila cuaca berubah.',
                $this->joinReasons(['peluang hujan malam maks. '.$nightChance.'%'.($peakNight ? ' sekitar '.$peakNight : ''), 'angin maks. '.number_format($wind, 1, ',', '.').' km/jam', 'hujan per jam maks. '.number_format($maxHourlyRain, 1, ',', '.').' mm'])
            );
        }

        $airRisk = $pm25 !== null && $pm25 > 55.4;
        $outdoorRisk = $airRisk || $chance >= 50 || $storm || ($feels !== null && $feels >= 36) || ($uv !== null && $uv >= 8);
        $outdoorStatus = $outdoorRisk ? 'waspada' : ($pm25 === null ? 'informasi' : 'baik');
        $advice['olahraga_rekreasi'] = $this->item(
            $outdoorStatus,
            $outdoorRisk ? 'Pilih aktivitas dalam ruang atau tunda olahraga sore, terutama saat udara berpartikulat tinggi atau badai/hujan mendekat.' : ($pm25 === null ? 'Data partikulat belum tersedia untuk memastikan kualitas udara bagi olahraga luar ruang.' : 'Cuaca dan partikulat belum melewati ambang waspada untuk olahraga luar ruang.'),
            $this->joinReasons(['PM2.5 '.($pm25 === null ? 'belum tersedia' : number_format($pm25, 1, ',', '.').' µg/m³'.($airRisk ? ' (melewati 55,4)' : '')), 'peluang hujan maks. '.$chance.'%', $feels === null ? null : 'terasa '.number_format($feels, 1, ',', '.').'°C', $uv === null ? null : 'indeks UV '.number_format($uv, 1, ',', '.'), $storm ? 'potensi badai petir' : null])
        );

        $constructionRisk = $heavyRain || $storm || ($rainTotal >= 30 && (int) ($weather->rain_hours_over_1mm ?? 0) >= 3);
        $advice['konstruksi_lapangan'] = $this->item(
            $constructionRisk ? 'waspada' : 'baik',
            $constructionRisk ? 'Pertimbangkan menghentikan pengecoran atau penggalian terbuka; amankan pekerja dari petir, tanah becek, dan lereng pematangan lahan yang rawan.' : 'Belum ada pemicu hujan ekstrem atau badai petir pada prakiraan; evaluasi kondisi lapangan langsung.',
            $this->joinReasons(['hujan per jam maks. '.number_format($maxHourlyRain, 1, ',', '.').' mm', 'akumulasi hujan prakiraan 24 jam '.number_format($rainTotal, 1, ',', '.').' mm', $storm ? 'potensi badai petir' : null])
        );

        $laundryRisk = $chance >= 40 || $maxHourlyRain >= 1 || ($humidity !== null && $humidity >= 80);
        $advice['jemuran_pakaian'] = $this->item(
            $laundryRisk ? 'waspada' : 'baik',
            $laundryRisk ? 'Jemur di tempat beratap atau angkat sebelum berangkat kerja; pantau pembaruan cuaca berkala agar pakaian tidak kehujanan.' : 'Peluang hujan relatif rendah untuk menjemur di luar, tetapi cuaca dapat berubah cepat.',
            $this->joinReasons(['peluang hujan maks. '.$chance.'%'.($peakEvening ? ' sore sekitar '.$peakEvening : ''), 'kelembapan maks. '.($humidity === null ? '—' : number_format($humidity, 0, ',', '.').'%'), 'hujan per jam maks. '.number_format($maxHourlyRain, 1, ',', '.').' mm'])
        );

        return $advice;
    }

    private function value(?object $object, string $key, mixed $default = null): ?float
    {
        if (! $object || ! isset($object->{$key}) || ! is_numeric($object->{$key})) {
            return $default === null ? null : (float) $default;
        }

        return (float) $object->{$key};
    }

    private function hour(mixed $value): ?string
    {
        return $value ? Carbon::parse($value)->timezone('Asia/Jakarta')->format('H.i') : null;
    }

    private function direction(?float $degrees): ?string
    {
        if ($degrees === null) {
            return null;
        }

        return ['Utara', 'Timur Laut', 'Timur', 'Tenggara', 'Selatan', 'Barat Daya', 'Barat', 'Barat Laut'][((int) round($degrees / 45)) % 8];
    }

    private function rainReason(object $weather, int $chance, ?string $time): string
    {
        $probability = 'peluang hujan maks. '.$chance.'%'.($time ? ' sekitar '.$time : ' dalam 24 jam');
        $amount = isset($weather->max_rain_hourly_mm)
            ? ' · hujan per jam maks. '.number_format((float) $weather->max_rain_hourly_mm, 1, ',', '.').' mm'
            : '';

        return $probability.$amount;
    }

    private function joinReasons(array $parts): string
    {
        return implode(' · ', array_values(array_filter($parts, fn ($part) => $part !== null && $part !== '')));
    }

    private function item(string $status, string $message, string $reason): array
    {
        return ['status' => $status, 'message' => $message, 'reason' => $reason];
    }
}
