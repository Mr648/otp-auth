<?php

namespace RahmatWaisi\OtpAuth\Facades;

use RahmatWaisi\OtpAuth\Core\OtpBuilder;
use RahmatWaisi\OtpAuth\Core\OtpResolver;
use RahmatWaisi\OtpAuth\Core\OtpType;
use RahmatWaisi\OtpAuth\OtpService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static OtpResolver resolver()
 * @method static OtpBuilder builder()
 *
 * @method static bool verify(string $key, $otp): bool
 * @method static bool verifyUsing(string $prefix, string $key, $otp): bool
 *
 * @method static bool remove(string $key, $otp)
 * @method static bool forget(string $key)
 * @method static string|int get(string $key)
 *
 * @method static string|int create(string $key)
 * @method static string|int createFrom(string $prefix, string $key, OtpType $type = OtpType::NUMBER, int $length = 6, $ttl = null)
 *
 * @see OtpService
 */
class OtpGenerator extends Facade
{
    public const ACCESSOR = 'otp';

    protected static function getFacadeAccessor(): string
    {
        return self::ACCESSOR;
    }
}
