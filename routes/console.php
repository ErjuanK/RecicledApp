<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Refresh the editorial feed every Monday at 06:00 AM
// Run manually anytime with: php artisan releases:refresh --force
Schedule::command('releases:refresh')->weeklyOn(1, '6:00')->withoutOverlapping();
