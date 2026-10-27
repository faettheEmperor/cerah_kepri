<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $path = __DIR__.'/data/kepri_regions.json';
        $regions = json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
        $now = now();

        // Parent rows are inserted first to satisfy the self-referencing FK.
        foreach (['province', 'regency', 'city', 'district', 'village'] as $type) {
            $rows = array_map(static fn (array $region): array => [
                'code' => $region['code'],
                'name' => $region['name'],
                'type' => $region['type'],
                'parent_code' => $region['parent_code'],
                'latitude' => $region['latitude'],
                'longitude' => $region['longitude'],
                'active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ], array_values(array_filter($regions, static fn (array $region): bool => $region['type'] === $type)));

            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table('regions')->upsert(
                    $chunk,
                    ['code'],
                    ['name', 'type', 'parent_code', 'latitude', 'longitude', 'active', 'updated_at'],
                );
            }
        }
    }
}
