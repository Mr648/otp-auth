<?php

namespace Tests\Unit;

use RahmatWaisi\OtpAuth\Exceptions\OtpInvalidException;
use RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException;
use RahmatWaisi\OtpAuth\Facades\OtpGenerator;
use Orchestra\Testbench\TestCase;
use RahmatWaisi\OtpAuth\Providers\OtpServiceProvider;


class OtpResolverTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [
            OtpServiceProvider::class,
        ];
    }

    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::create
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::verify
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::get
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::forget
     */
    public function test_resolver_with_default_settings(): void
    {
        $key = 'id';
        $cacheKey = sprintf('%s_%s', config('otp.cache.prefix'), $key);

        $otp = OtpGenerator::create($key);
        $this->assertIsInt($otp);
        $this->assertTrue(cache()->has($cacheKey));

        $this->assertTrue(OtpGenerator::verify($key, $otp));
        $this->assertTrue(OtpGenerator::get($key) === $otp);

        OtpGenerator::forget($key);
        $this->assertTrue(cache()->missing($cacheKey));


        $otp = OtpGenerator::create($key);
        $this->assertTrue(OtpGenerator::get($key) === $otp);
        $this->assertTrue(OtpGenerator::remove($key, $otp));
        $this->assertTrue(cache()->missing($cacheKey));
    }


    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::get
     * @covers \RahmatWaisi\OtpAuth\Exceptions\OtpInvalidException
     */
    public function test_resolver_throwing_invalid_otp_exception(): void
    {
        $key = 'id';
        $this->expectException(OtpInvalidException::class);
        $otp = OtpGenerator::get($key);
    }


    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::resolver
     * @covers \RahmatWaisi\OtpAuth\Core\OtpResolver::resolve
     * @covers \RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException
     */
    public function test_resolver_throwing_malformed_otp_exception_undefined_key(): void
    {
        $this->expectException(OtpMalformedException::class);
        $this->expectExceptionMessage('OTP unique key is undefined.');
        OtpGenerator::resolver()->resolve();
    }


    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::resolver
     * @covers \RahmatWaisi\OtpAuth\Core\OtpResolver::resolve
     * @covers \RahmatWaisi\OtpAuth\Core\OtpResolver::withKey
     * @covers \RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException
     */
    public function test_resolver_throwing_malformed_otp_exception_undefined_prefix(): void
    {
        $key = 'id';
        $this->expectException(OtpMalformedException::class);
        $this->expectExceptionMessage('OTP prefix is undefined.');
        OtpGenerator::resolver()->withKey($key)->resolve();
    }

    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::createFrom
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::resolver
     * @covers \RahmatWaisi\OtpAuth\Core\OtpResolver::resolve
     * @covers \RahmatWaisi\OtpAuth\Core\OtpResolver::withKey
     * @covers \RahmatWaisi\OtpAuth\Core\OtpResolver::withPrefix
     * @covers \RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException
     */
    public function test_resolver_with_custom_settings(): void
    {
        $length = 8;
        $prefix = 'dummy_prefix';
        $key = 'my_custom_key';
        $cacheKey = sprintf('%s_%s', $prefix, $key);

        $otp = OtpGenerator::createFrom($prefix, $key);
        $this->assertIsInt($otp);
        $this->assertTrue(cache()->has($cacheKey));

        // Check using resolve method
        $resolvedOtp = OtpGenerator::resolver()
            ->withPrefix($prefix)
            ->withKey($key)
            ->resolve();

        $this->assertTrue($resolvedOtp === $otp);
        $this->assertTrue(cache()->has($cacheKey));

        // Check using forget method
        $forgotten = OtpGenerator::resolver()
            ->withPrefix($prefix)
            ->withKey($key)
            ->forget();

        $this->assertTrue($forgotten);
        $this->assertTrue(cache()->missing($cacheKey));


        // Check using exists method

        $otp = OtpGenerator::createFrom($prefix, $key);
        $this->assertIsInt($otp);
        $this->assertTrue(cache()->has($cacheKey));

        $exists = OtpGenerator::resolver()
            ->withPrefix($prefix)
            ->withKey($key)
            ->exists($otp);

        $this->assertTrue($exists);
        $this->assertTrue(cache()->has($cacheKey));
    }
}
