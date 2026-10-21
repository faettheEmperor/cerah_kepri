<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $regions = [
            ['21.72', 'Kota Tanjung Pinang', 'city', null, 0.9167184841609475, 104.46502674492163],
            ['21.72.04', 'Bukit Bestari', 'district', '21.72', 0.8672960730659458, 104.49713470111695],
            ['21.72.04.1002', 'Dompak', 'village', '21.72.04', 0.8674494530460318, 104.493014828146],
            ['21.72.04.1004', 'Sei Jang', 'village', '21.72.04', 0.9050715474309479, 104.47065610298766],
            ['21.72.04.1003', 'Tanjung Ayun Sakti', 'village', '21.72.04', 0.9018033292785638, 104.45922464817104],
            ['21.72.04.1001', 'Tanjung Pinang Timur', 'village', '21.72.04', 0.9152745527, 104.4582804261],
            ['21.72.04.1005', 'Tanjung Unggat', 'village', '21.72.04', 0.923779744301384, 104.46101451877792],
            ['21.72.01', 'Tanjung Pinang Barat', 'district', '21.72', 0.9186050168470921, 104.44453306351454],
            ['21.72.01.1004', 'Bukit Cermin', 'village', '21.72.01', 0.9208107667069828, 104.44825261931311],
            ['21.72.01.1003', 'Kampung Baru', 'village', '21.72.01', 0.909024639242704, 104.44980437564688],
            ['21.72.01.1002', 'Kemboja', 'village', '21.72.01', 0.9254616877282823, 104.4516067748441],
            ['21.72.01.1001', 'Tanjung Pinang Barat', 'village', '21.72.01', 0.9158712309, 104.4422068093],
            ['21.72.03', 'Tanjung Pinang Kota', 'district', '21.72', 0.9596304230015124, 104.44790683804924],
            ['21.72.03.1005', 'Kampung Bugis', 'village', '21.72.03', 0.9600995105291529, 104.4558297649851],
            ['21.72.03.1004', 'Penyengat', 'village', '21.72.03', 0.9279133611419694, 104.42139841556684],
            ['21.72.03.1003', 'Senggarang', 'village', '21.72.03', 0.9666200634980029, 104.42537407881615],
            ['21.72.03.1001', 'Tanjung Pinang Kota', 'village', '21.72.03', 0.9293265651, 104.4415661978],
            ['21.72.02', 'Tanjung Pinang Timur', 'district', '21.72', 0.9119888113776845, 104.50901861410442],
            ['21.72.02.1003', 'Air Raja', 'village', '21.72.02', 0.9533361837367531, 104.49622648153387],
            ['21.72.02.1004', 'Batu IX (Sembilan)', 'village', '21.72.02', 0.9025455413281114, 104.5173073888496],
            ['21.72.02.1002', 'Kampung Bulang', 'village', '21.72.02', 0.9192788448595707, 104.47007202435312],
            ['21.72.02.1001', 'Melayu Kota Piring', 'village', '21.72.02', 0.9172623280155602, 104.48803168487997],
            ['21.72.02.1005', 'Pinang Kencana', 'village', '21.72.02', 0.929661629625583, 104.52872120664557],
        ];

        foreach ($regions as [$code, $name, $type, $parentCode, $latitude, $longitude]) {
            DB::table('regions')->updateOrInsert(['code' => $code], [
                'name' => $name,
                'type' => $type,
                'parent_code' => $parentCode,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
