<?php

use App\Jobs\SyncCustomerPricelistsJob;
use App\Jobs\SyncProductsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new SyncProductsJob)
    ->hourly();

Schedule::job(new SyncCustomerPricelistsJob)
    ->hourly();
