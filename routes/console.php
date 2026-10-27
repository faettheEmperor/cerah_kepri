<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Weather updates are requested for the selected region from the page. Avoid
// synchronizing all 505 Kepri points on a single public API quota.
Schedule::command('weather:aggregate-daily')->everyThirtyMinutes()->withoutOverlapping();
Schedule::command('news:sync')->everyThirtyMinutes()->withoutOverlapping();
Schedule::command('weather:prune')->hourly()->withoutOverlapping();
Schedule::command('climate:sync-history --years=10')->monthlyOn(2, '03:20')->withoutOverlapping();
Schedule::command('climate:sync-history --recent-days=10')->dailyAt('05:30')->withoutOverlapping();
