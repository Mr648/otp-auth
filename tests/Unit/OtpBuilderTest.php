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

        $otp = OtpGenerator::createFrom('smth', 'id');
        $this->assertIsInt($otp);
        $this->assertTrue(strlen(strval($otp))  === config('otp.length')); // ensure otp has 6 digits
        $this->assertTrue(cache()->has("smth_id"));

        $otp = OtpGenerator::createFrom('smth', 'email');
        $this->assertIsInt($otp);
        $this->assertTrue(cache()->has("smth_email"));


        $otp = OtpGenerator::createFrom('smth', 'username', OtpType::STRING);
        $this->assertIsNotInt($otp);
        $this->assertTrue(strlen($otp) === config('otp.length'));
        $this->assertTrue(is_string($otp));
        $this->assertTrue(cache()->has("smth_username"));


        $length = 12;
        $otp = OtpGenerator::createFrom('smth', 'custom_key', OtpType::NUMBER, $length, now()->addDay());
        $this->assertIsInt($otp);
        $this->assertTrue(strlen(strval($otp))  === $length); // ensure otp has 6 digits
        $this->assertTrue(cache()->has("smth_custom_key"));

        $length = 8;
        $otp = OtpGenerator::createFrom('smth', 'whatever', OtpType::STRING, $length, now()->addDay());
        $this->assertIsNotInt($otp);
        $this->assertTrue(strlen($otp) === $length);
        $this->assertTrue(is_string($otp));
        $this->assertTrue(cache()->has("smth_whatever"));
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
        $length = 8;
        $otp = OtpGenerator::builder()
            ->withPrefix('dummy_prefix')
            ->withKey('my_custom_key')
            ->withType(OtpType::NUMBER)
            ->withTtl(now()->addMinutes(2))
            ->withLength($length)
            ->build();

        $this->assertIsInt($otp);
        $this->assertTrue(strlen(strval($otp))  === $length); // ensure otp has 6 digits


        $length = 12;
        $otp = OtpGenerator::builder()
            ->withPrefix('dummy_prefix')
            ->withKey('my_custom_key')
            ->withType(OtpType::STRING)
            ->withTtl(now()->addMinutes(2))
            ->withLength($length)
            ->build();

        $this->assertIsNotInt($otp);
        $this->assertTrue(strlen($otp) === $length);
        $this->assertTrue(is_string($otp));

        $this->assertTrue(cache()->has("dummy_prefix_my_custom_key"));

        $otp = OtpGenerator::builder()
            ->withDefaultSettings()
            ->withKey('my_custom_key')
            ->build();

        $this->assertIsInt($otp);
        $this->assertTrue(strlen($otp) === config('otp.length'));
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
