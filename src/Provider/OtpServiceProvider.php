<?php

namespace RahmatWaisi\OtpAuth\Provider;

use RahmatWaisi\OtpAuth\Facades\OtpGenerator;
use RahmatWaisi\OtpAuth\OtpService;
use Illuminate\Support\ServiceProvider;

class OtpServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(OtpGenerator::ACCESSOR, fn() => new OtpService());
    }

    public function boot()
    {
        $config = __DIR__ . '/../config/otp.php';

        $this->publishes([
            $config => config_path('otp.php'),
        ], 'otp-config');
    }
}
