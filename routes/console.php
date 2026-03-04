<?php

use App\Services\AttendanceCheck;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:daily-task', function () {
    (new AttendanceCheck())->checkAttendance();
})->purpose('Run the daily function');

Schedule::command('app:daily-task')->dailyAt('05:00');
