<?php

namespace Tests\Unit;

use RahmatWaisi\OtpAuth\Exceptions\OtpInvalidException;
use RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException;
use RahmatWaisi\OtpAuth\Core\OtpType;
use RahmatWaisi\OtpAuth\Facades\OtpGenerator;
use Orchestra\Testbench\TestCase;
use RahmatWaisi\OtpAuth\Providers\OtpServiceProvider;

class OtpBuilderTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [
            OtpServiceProvider::class,
        ];
    }

    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::createFrom
     */
    public function test_builder_with_default_settings(): void
    {
        $prefix = 'something';
        $key = 'id'; // $user->id
        $otp = OtpGenerator::createFrom($prefix, $key);
        $this->assertIsInt($otp);
        $this->assertTrue(strlen(strval($otp))  === config('otp.length')); // ensure otp has 6 digits
        $this->assertTrue(cache()->has(md5(sprintf('%s_%s', $prefix, $key))));

        $key = 'email'; // $user->email
        $otp = OtpGenerator::createFrom($prefix, $key);
        $this->assertIsInt($otp);
        $this->assertTrue(cache()->has(md5(sprintf('%s_%s', $prefix, $key))));


        $key = 'username';// $user->username
        $otp = OtpGenerator::createFrom($prefix, $key, OtpType::STRING);
        $this->assertIsNotInt($otp);
        $this->assertTrue(strlen($otp) === config('otp.length'));
        $this->assertTrue(is_string($otp));
        $this->assertTrue(cache()->has(md5(sprintf('%s_%s', $prefix, $key))));


        $length = 12;
        $key = 'custom_key';
        $otp = OtpGenerator::createFrom($prefix, $key, OtpType::NUMBER, $length, now()->addDay());
        $this->assertIsInt($otp);
        $this->assertTrue(strlen(strval($otp))  === $length); // ensure otp has 6 digits
        $this->assertTrue(cache()->has(md5(sprintf('%s_%s', $prefix, $key))));

        $length = 8;
        $key = 'whatever';
        $otp = OtpGenerator::createFrom($prefix, $key, OtpType::STRING, $length, now()->addDay());
        $this->assertIsNotInt($otp);
        $this->assertTrue(strlen($otp) === $length);
        $this->assertTrue(is_string($otp));
        $this->assertTrue(cache()->has(md5(sprintf('%s_%s', $prefix, $key))));
    }

    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::builder
     * @covers \RahmatWaisi\OtpAuth\Core\OtpBuilder::withPrefix
     * @covers \RahmatWaisi\OtpAuth\Core\OtpBuilder::withKey
     * @covers \RahmatWaisi\OtpAuth\Core\OtpBuilder::withType
     * @covers \RahmatWaisi\OtpAuth\Core\OtpBuilder::withTtl
     * @covers \RahmatWaisi\OtpAuth\Core\OtpBuilder::withLength
     * @covers \RahmatWaisi\OtpAuth\Core\OtpBuilder::withDefaultSettings
     */
    public function test_builder_with_custom_settings(): void
    {
        $prefix = 'dummy_prefix';
        $key = 'my_custom_key'; // $user->id

        $length = 8;
        $otp = OtpGenerator::builder()
            ->withPrefix($prefix)
            ->withKey($key)
            ->withType(OtpType::NUMBER)
            ->withTtl(now()->addMinutes(2))
            ->withLength($length)
            ->build();

        $this->assertIsInt($otp);
        $this->assertTrue(strlen(strval($otp))  === $length); // ensure otp has 6 digits
        $this->assertTrue(cache()->has(md5(sprintf('%s_%s', $prefix, $key))));

        $length = 12;
        $key = 'anotherthing';
        $otp = OtpGenerator::builder()
            ->withPrefix($prefix)
            ->withKey($key)
            ->withType(OtpType::STRING)
            ->withTtl(now()->addMinutes(2))
            ->withLength($length)
            ->build();

        $this->assertIsNotInt($otp);
        $this->assertTrue(strlen($otp) === $length);
        $this->assertTrue(is_string($otp));

        $this->assertTrue(cache()->has(md5(sprintf('%s_%s', $prefix, $key))));

        $key = 'something_else';
        $otp = OtpGenerator::builder()
            ->withDefaultSettings()
            ->withKey($key)
            ->build();

        $this->assertIsInt($otp);
        $this->assertTrue(strlen($otp) === config('otp.length'));
        $this->assertTrue(cache()->has(md5(sprintf('%s_%s', config('otp.cache.prefix'), $key))));
    }

    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::get
     * @covers \RahmatWaisi\OtpAuth\Exceptions\OtpInvalidException
     */
    public function test_builder_throwing_invalid_otp_exception(): void
    {
        $key = 'id';
        $this->expectException(OtpInvalidException::class);
        $otp = OtpGenerator::get($key);
    }

    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::builder
     * @covers \RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException
     */
    public function test_builder_throwing_malformed_otp_exception_undefined_key(): void
    {
        $this->expectException(OtpMalformedException::class);
        $this->expectExceptionMessage('OTP unique key is undefined.');
        $otp = OtpGenerator::builder()
            ->withPrefix('dummy_prefix')
            ->withType(OtpType::STRING)
            ->withTtl(now()->addMinutes(2))
            ->withDefaultLength()
            ->build();
    }

    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::builder
     * @covers \RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException
     */
    public function test_builder_throwing_malformed_otp_exception_undefined_prefix(): void
    {
        $this->expectException(OtpMalformedException::class);
        $this->expectExceptionMessage('OTP prefix is undefined.');
        $otp = OtpGenerator::builder()
            ->withDefaultLength()
            ->withKey('my_custom_key')
            ->withType(OtpType::STRING)
            ->withTtl(now()->addMinutes(2))
            ->build();
    }


    /**
     * @covers \RahmatWaisi\OtpAuth\Facades\OtpGenerator::builder
     * @covers \RahmatWaisi\OtpAuth\Exceptions\OtpMalformedException
     */
    public function test_builder_throwing_malformed_otp_exception_undefined_length(): void
    {
        $this->expectException(OtpMalformedException::class);
        $this->expectExceptionMessage('OTP length is undefined.');
        $otp = OtpGenerator::builder()
            ->withPrefix('dummy_prefix')
            ->withKey('my_custom_key')
            ->withType(OtpType::STRING)
            ->withTtl(now()->addMinutes(2))
            ->build();
    }
}
