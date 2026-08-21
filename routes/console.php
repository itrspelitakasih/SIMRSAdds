<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Server timezone is UTC; 00:00 UTC = 07:00 WIB (Asia/Jakarta).
Schedule::command('documents:send-reminders')->dailyAt('00:00');
