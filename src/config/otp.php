<?php


use RahmatWaisi\OtpAuth\Core\OtpType;

return [
    'cache' => [
        'prefix' => env('OTP_CACHE_PREFIX', 'otp'),

        /* OTP time to live - 1 Day */
        'ttl' => env('OTP_TTL', 86400),
    ],

    'type' => env('OTP_TYPE', OtpType::NUMBER->value),

    'length' => env('OTP_LENGTH', 6),
];
