<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Backup terjadwal (Spatie Backup)
use Illuminate\Support\Facades\Schedule;

// Bersihkan backup lama setiap hari jam 01:00
Schedule::command('backup:clean')->dailyAt('01:00');
// Lakukan backup (DB dan files) setiap hari jam 02:00
Schedule::command('backup:run')->dailyAt('02:00');
