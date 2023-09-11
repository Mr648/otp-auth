<?php

namespace RahmatWaisi\OtpAuth\Core;

use RahmatWaisi\OtpAuth\Exceptions\OtpInvalidException;
use RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException;
use Throwable;

class OtpResolver extends OtpUtilities
{
    /**
     * @return string|int
     * @throws OtpInvalidException
     * @throws OtpMalformedException
     * @throws Throwable
     */
    public function resolve()
    {
        $this->validate();
        $cacheKey = $this->getCacheKey();
        throw_if(cache()->missing($cacheKey), new OtpInvalidException('Otp is invalid.'));
        return cache()->get($cacheKey);
    }

    /**
     * @return bool
     * @throws OtpInvalidException
     * @throws OtpMalformedException
     * @throws Throwable
     */
    public function forget(): bool
    {
        $this->validate();
        $cacheKey = $this->getCacheKey();

        throw_if(cache()->missing($cacheKey), new OtpInvalidException('Otp is invalid.'));

        return cache()->forget($cacheKey);
    }

    /**
     * @throws Throwable
     * @throws OtpMalformedException
     * @throws OtpInvalidException
     */
    public function exists($otp): bool
    {
        $this->validate();
        $cacheKey = $this->getCacheKey();
        return cache()->has($cacheKey) && cache()->get($cacheKey) === $otp;
    }

    /**
     * @throws Throwable
     * @throws OtpInvalidException
     * @throws OtpMalformedException
     */
    public function remove($otp): bool
    {
        $this->validate();
        $cacheKey = $this->getCacheKey();

        throw_if(
            cache()->missing($cacheKey) || cache()->get($cacheKey) !== $otp,
            new OtpInvalidException('Otp is invalid.')
        );

        return cache()->forget($cacheKey);
    }

    public function withDefaultSettings(): self
    {
        return $this->withDefaultPrefix();
    }
}
