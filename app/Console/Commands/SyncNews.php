<?php

namespace App\Console\Commands;

use App\Services\NewsFeedService;
use Illuminate\Console\Command;

class SyncNews extends Command
{
    protected $signature = 'news:sync';

    protected $description = 'Ambil berita RSS Kepri yang cocok dengan kata kunci cuaca dan lingkungan';

    public function handle(NewsFeedService $newsFeedService): int
    {
        $result = $newsFeedService->sync();
        $this->info($result['written'].' artikel cocok disimpan atau diperbarui.');

        foreach ($result['errors'] as $error) {
            $this->error($error);
        }

        return $result['errors'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
