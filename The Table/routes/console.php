<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule weekly profit distribution every Friday at 5 PM
Schedule::command('profits:distribute-weekly')
    ->weeklyOn(5, '17:00') // Friday at 5 PM
    ->timezone('Africa/Johannesburg')
    ->description('Distribute weekly partnership profits (50% admin, 50% partners)');
