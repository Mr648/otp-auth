<?php

namespace RahmatWaisi\OtpAuth\Providers;

use Illuminate\Support\ServiceProvider;
use RahmatWaisi\OtpAuth\Facades\OtpGenerator;
use RahmatWaisi\OtpAuth\OtpService;

class OtpServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'otp');

        $this->app->bind(OtpGenerator::ACCESSOR, fn() => new OtpService());
    }

    public function boot()
    {
        $config = __DIR__ . '/../config/config.php';

        $this->publishes([
            $config => config_path('config.php'),
        ], 'otp-config');
    }
}
