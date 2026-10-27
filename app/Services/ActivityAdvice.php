<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class ActivityAdvice
{
    public function forWeather(?object $weather): array
    {
        if (! $weather) {
            return [];
        }

        $chance = (int) ($weather->rain_chance_24h ?? $weather->precipitation_probability ?? 0);
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
        $peakNight = $this->hour($weather->night_rain_at ?? null);
        $heavyRain = $maxHourlyRain > 20;
        $windKnots = $wind / 1.852;
        $coastalCode = (string) ($weather->region_code ?? '');
        $coastalCodes = ['21.72.03.1001', '21.72.03.1003', '21.72.03.1004', '21.72.03.1005', '21.72.04.1004', '21.72.04.1005'];
        $isCoastal = ($weather->region_type ?? null) === 'city' || in_array($coastalCode, $coastalCodes, true);
        $advice = [];

        if ($isCoastal) {
            $crossingRisk = $wind >= 37 || ($visibility !== null && $visibility < 1000) || $heavyRain || $storm;
            $crossingReason = $this->joinReasons([
                'angin '.number_format($wind, 1, ',', '.').' km/jam ('.number_format($windKnots, 1, ',', '.').' knot)',
                $windDirection === null ? null : 'arah angin '.$windDirection,
                $visibility === null ? null : 'jarak pandang '.number_format($visibility / 1000, 1, ',', '.').' km',
                'hujan per jam maks. '.number_format($maxHourlyRain, 1, ',', '.').' mm',
                $storm ? 'potensi badai petir' : null,
            ]);
            $advice['penyeberangan_pompong'] = $this->item(
                $crossingRisk ? 'waspada' : 'informasi',
                $crossingRisk
                    ? 'Cuaca berpotensi mengganggu penyeberangan Tanjungpinang–Penyengat/Kampung Bugis. Ikuti arahan operator dan tunda perjalanan bila layanan dihentikan.'
                    : 'Prakiraan belum menunjukkan kondisi cuaca darat yang melewati ambang waspada. Tetap konfirmasi jadwal dan kondisi langsung kepada operator pompong.',
                $crossingReason.' · Periksa kondisi langsung dan konfirmasi layanan operator sebelum menyeberang.'
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

        $soilMoisture = $this->value($weather, 'soil_moisture_0_1cm');
        $temperature = $this->value($weather, 'temperature_c');
        $agricultureRisk = $storm || $heavyRain || $rainTotal >= 30;
        $soilIsDry = $soilMoisture !== null && $soilMoisture < 0.15;
        $agricultureStatus = $agricultureRisk ? 'waspada' : ($soilIsDry ? 'saran' : (($soilMoisture === null && $rainTotal === 0 && $chance === 0) ? 'informasi' : 'baik'));
        $agricultureMessage = $agricultureRisk
            ? 'Tunda pemupukan atau penyemprotan, dan pastikan bedengan serta saluran kebun memiliki drainase yang baik saat hujan lebat atau badai diprakirakan.'
            : ($soilIsDry
                ? 'Kelembapan tanah lapisan atas cenderung rendah. Prioritaskan penyiraman secukupnya pada pagi atau sore dan sesuaikan dengan hujan setempat.'
                : 'Cuaca belum menunjukkan pemicu hujan ekstrem. Atur penyiraman dan pemupukan kebun sayur mengikuti kondisi tanah serta hujan aktual di lahan.');
        $advice['pertanian_sayur'] = $this->item(
            $agricultureStatus,
            $agricultureMessage,
            $this->joinReasons([
                'peluang hujan maks. '.$chance.'%',
                'akumulasi hujan 24 jam '.number_format($rainTotal, 1, ',', '.').' mm',
                'hujan per jam maks. '.number_format($maxHourlyRain, 1, ',', '.').' mm',
                $soilMoisture === null ? 'kelembapan tanah belum tersedia' : 'kelembapan tanah lapisan atas '.number_format($soilMoisture * 100, 0, ',', '.').'%',
                $temperature === null ? null : 'suhu '.number_format($temperature, 1, ',', '.').'°C',
                $storm ? 'potensi badai petir' : null,
            ])
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

    private function joinReasons(array $parts): string
    {
        $reasons = array_values(array_filter($parts, fn ($part) => $part !== null && $part !== ''));

        return implode(' · ', array_map(
            fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)).mb_substr($part, 1),
            $reasons,
        ));
    }

    private function item(string $status, string $message, string $reason): array
    {
        return ['status' => $status, 'message' => $message, 'reason' => $reason];
    }
}
