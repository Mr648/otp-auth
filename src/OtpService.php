<?php

namespace RahmatWaisi\OtpAuth;

use RahmatWaisi\OtpAuth\Core\OtpBuilder;
use RahmatWaisi\OtpAuth\Core\OtpResolver;
use RahmatWaisi\OtpAuth\Core\OtpType;
use RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException;
use Throwable;

class OtpService
{
    public function resolver(): OtpResolver
    {
        return new OtpResolver();
    }

    public function builder(): OtpBuilder
    {
        return new OtpBuilder();
    }

    /**
     * @throws Throwable
     * @throws OtpMalformedException
     */
    public function verify(string $key, $otp): bool
    {
        return $this->resolver()->withDefaultSettings()->withKey($key)->exists($otp);
    }

    /**
     * @throws Throwable
     * @throws OtpMalformedException
     */
    public function remove(string $key, $otp): bool
    {
        return $this->resolver()->withDefaultSettings()->withKey($key)->remove($otp);
    }

    /**
     * @throws Throwable
     * @throws OtpMalformedException
     */
    public function forget(string $key): bool
    {
        return $this->resolver()->withDefaultSettings()->withKey($key)->forget();
    }

    /**
     * @throws Throwable
     * @throws OtpMalformedException
     * @return string|null
     */
    public function get(string $key)
    {
        return $this->resolver()->withDefaultSettings()->withKey($key)->resolve();
    }

    /**
     * @throws Throwable
     * @throws OtpMalformedException
     */
    public function verifyUsing(string $prefix, string $key, $otp): bool
    {
        return $this->resolver()->withPrefix($prefix)->withKey($key)->exists($otp);
    }

    /**
     * @throws Throwable
     * @throws OtpMalformedException
     * @return string|int
     */
    public function create(string $key)
    {
        return $this->builder()
            ->withDefaultSettings()
            ->withKey($key)
            ->build();
    }

    /**
     * @throws Throwable
     * @throws OtpMalformedException
     * @return string|int
     */
    public function createFrom(string $prefix, string $key, OtpType $type = OtpType::NUMBER, int $length = 6, $ttl = null)
    {
        return $this->builder()
            ->withPrefix($prefix)
            ->withTtl($ttl)
            ->withType($type)
            ->withLength($length)
            ->withKey($key)->build();
    }
}
