<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Command that run in crontab scheduler example
Schedule::command('app:generate-employee-time-sheet')->everyMinute();
