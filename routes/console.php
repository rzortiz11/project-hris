<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $quote = Inspiring::quote();
    $this->comment($quote);

    Log::info('Inspire command ran: ' . $quote);
})->purpose('Display an inspiring quote')->everyMinute();
