<?php

namespace RahmatWaisi\OtpAuth\Core;

use RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException;
use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Str;
use Throwable;

class OtpBuilder extends OtpUtilities
{
    /**
     * @var DateInterval|DateTimeInterface|int|null
     */
    protected $ttl = null;

    /**
     * @var OtpType
     */
    protected OtpType $type = OtpType::NUMBER;

    /**
     * @var int|null
     */
    private $length = null;

    /**
     * @param DateInterval|DateTimeInterface|int|null $ttl
     * @return OtpBuilder
     */
    public function withTtl($ttl): OtpBuilder
    {
        $this->ttl = $ttl;
        return $this;
    }

    /**
     * @return OtpBuilder
     */
    public function withDefaultTtl(): OtpBuilder
    {
        $this->ttl = config('otp.cache.ttl');
        return $this;
    }

    /**
     * @return OtpBuilder
     */
    public function withNoTtl(): OtpBuilder
    {
        $this->ttl = null;
        return $this;
    }

    /**
     * @param OtpType $type
     * @return OtpBuilder
     */
    public function withType(OtpType $type = OtpType::NUMBER): OtpBuilder
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param int $length
     * @return OtpBuilder
     */
    public function withLength(int $length): OtpBuilder
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return OtpBuilder
     */
    public function withDefaultLength(): OtpBuilder
    {
        $this->length = config('otp.length');
        return $this;
    }

    public function validate(): void
    {
        parent::validate();
        throw_if(empty($this->length), new OtpMalformedException('OTP length is undefined.'));
    }

    /**
     * @return string|int
     */
    private function createOtp()
    {
        return match ($this->type) {
            OtpType::STRING => Str::random($this->length),
            default => rand(pow(10, $this->length-1), pow(10, $this->length) - 1)
        };
    }

    /**
     * @param string|int $otp
     * @return bool|mixed
     */
    private function cacheOtp($otp)
    {
        $cacheKey = $this->getCacheKey();

        cache()->forget($cacheKey);

        return empty($this->ttl)
            ? cache()->rememberForever($cacheKey, fn() => $otp)
            : cache()->remember($cacheKey, $this->ttl, fn() => $otp);
    }

    /**
     * @throws Throwable
     * @throws OtpMalformedException
     */
    public function build()
    {
        $this->validate();
        return $this->cacheOtp($this->createOtp());
    }

    public function withDefaultSettings(): OtpBuilder
    {
        return $this->withDefaultPrefix()
            ->withDefaultTtl()
            ->withDefaultLength();
    }
}
