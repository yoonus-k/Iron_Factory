<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule: معالجة العمليات المعلقة كل 5 دقائق
Schedule::command('sync:process-pending')->everyFiveMinutes()->withoutOverlapping();
