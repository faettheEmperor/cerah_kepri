<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Services\ProcessingLog;
use Throwable;

class NewsFeedService
{
    /** @return array{written:int, errors:array<int,string>} */
    public function sync(): array
    {
        $written = 0;
        $errors = [];
        $feedsProcessed = 0;
        $feedsSucceeded = 0;
        $logId = ProcessingLog::start('news', 'Feed RSS ANTARA Kepri');
        $keywords = collect(config('news.keywords', []))
            ->map(fn ($keyword) => trim((string) $keyword))
            ->filter()
            ->unique()
            ->values();

        foreach (config('news.feeds', []) as $feed) {
            $feedsProcessed++;
            try {
                $response = Http::accept('application/rss+xml, application/xml, text/xml')
                    ->connectTimeout(8)
                    ->timeout(20)
                    ->get($feed['url']);

                if (! $response->successful()) {
                    throw new \RuntimeException('RSS membalas HTTP '.$response->status().'.');
                }

                $xml = $this->parseXml($response->body());
                $items = $xml->channel->item ?? [];
                $rows = [];
                foreach ($items as $item) {
                    $title = trim((string) ($item->title ?? ''));
                    $url = trim((string) ($item->link ?? ''));
                    if ($title === '' || ! filter_var($url, FILTER_VALIDATE_URL) || parse_url($url, PHP_URL_SCHEME) !== 'https') {
                        continue;
                    }

                    $description = (string) ($item->description ?? '');
                    $summary = $this->plainText($description);
                    $searchText = Str::lower($this->plainText($title.' '.$description));
                    $matched = $keywords->filter(fn ($keyword) => Str::contains($searchText, Str::lower($keyword)))->values();
                    if ($matched->isEmpty()) {
                        continue;
                    }

                    $publishedAt = $this->publishedAt((string) ($item->pubDate ?? ''));
                    $rows[] = [
                        'source_name' => mb_substr((string) ($feed['name'] ?? 'Sumber berita'), 0, 120),
                        'title' => mb_substr($title, 0, 500),
                        'summary' => $summary !== '' ? mb_substr($summary, 0, 320) : null,
                        'url' => mb_substr($url, 0, 1000),
                        'published_at' => $publishedAt,
                        'matched_keywords' => json_encode($matched->all(), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $rows = array_slice($rows, 0, max(1, (int) config('news.items_per_feed', 60)));
                foreach (array_chunk($rows, 100) as $chunk) {
                    DB::table('news_articles')->upsert(
                        $chunk,
                        ['url'],
                        ['source_name', 'title', 'summary', 'published_at', 'matched_keywords', 'updated_at'],
                    );
                    $written += count($chunk);
                }
                $feedsSucceeded++;
            } catch (Throwable $exception) {
                report($exception);
                $errors[] = (string) ($feed['name'] ?? $feed['url']).': '.$exception->getMessage();
            }
        }

        DB::table('news_articles')->where('published_at', '<', now()->subDays(max(1, (int) config('news.retention_days', 30))))->delete();
        Cache::forget('news:latest');
        ProcessingLog::finish($logId, $feedsProcessed, $feedsSucceeded, count($errors), $written, [
            'feed_names' => collect(config('news.feeds', []))->pluck('name')->values()->all(),
            'error_details' => $errors,
        ]);

        return ['written' => $written, 'errors' => $errors, 'feeds_processed' => $feedsProcessed, 'feeds_succeeded' => $feedsSucceeded];
    }

    private function parseXml(string $body): \SimpleXMLElement
    {
        $previous = libxml_use_internal_errors(true);
        try {
            $xml = simplexml_load_string($body, \SimpleXMLElement::class, LIBXML_NONET | LIBXML_NOCDATA | LIBXML_NOBLANKS);
            if (! $xml instanceof \SimpleXMLElement || ! isset($xml->channel)) {
                throw new \RuntimeException('Format RSS tidak dikenali.');
            }

            return $xml;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    private function plainText(string $value): string
    {
        $decoded = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $decoded));
    }

    private function publishedAt(string $value): Carbon
    {
        try {
            return $value !== '' ? Carbon::parse($value)->utc() : now()->utc();
        } catch (Throwable) {
            return now()->utc();
        }
    }
}
