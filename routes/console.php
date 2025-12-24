<?php

use App\Console\Commands\FetchDailyCurrencyRate;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Artisan::starting(function(){
//     app()->make(FetchDailyCurrencyRate::class);
// });
Schedule::command('currency:fetch-daily')
    ->dailyAt('01:00');
