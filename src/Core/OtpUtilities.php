<?php

namespace RahmatWaisi\OtpAuth\Core;

use RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException;
use Throwable;

abstract class OtpUtilities
{
    /**
     * @var string|null
     */
    protected $key;

    /**
     * @var string|null
     */
    protected $prefix = null;


    /**
     * @param string $key
     * @return self
     */
    public function withKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param string $prefix
     * @return self
     */
    public function withPrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * @return self
     */
    public function withDefaultPrefix(): self
    {
        $this->prefix = config('otp.cache.prefix');
        return $this;
    }

    protected function getCacheKey(): string
    {
        return sprintf('%s_%s', $this->prefix, $this->key);
    }

    abstract public function withDefaultSettings(): self;

    /**
     * @throws Throwable
     */
    public function validate(): void
    {
        throw_if(empty($this->key), new OtpMalformedException('OTP unique key is undefined.'));
        throw_if(empty($this->prefix), new OtpMalformedException('OTP prefix is undefined.'));
    }
}
