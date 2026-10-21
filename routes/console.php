<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('weather:sync')->everyThirtyMinutes()->withoutOverlapping();
Schedule::command('weather:prune')->hourly()->withoutOverlapping();
Schedule::command('climate:sync-history --years=10')->monthlyOn(2, '03:20')->withoutOverlapping();
