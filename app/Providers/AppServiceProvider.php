<?php

namespace App\Providers;

use App\Services\EsbApiAuth;
use App\Services\JurnalApi;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(JurnalApi::class, function ($app) {
            return new JurnalApi(
                config('Service.jurnal.username'),
                config('Service.jurnal.secret'),
                config('Service.jurnal.environment', 'sandbox')
            );
        });

        $this->app->singleton(EsbApiAuth::class, function () {
            return new EsbApiAuth(config('ESB.esb_username'), config('ESB.esb_password'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
