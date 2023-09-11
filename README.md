# One Time Password - OTP

<img src="https://banners.beyondco.de/Laravel%20OTP%20.png?theme=light&packageManager=composer+require&packageName=rahmatwaisi%2Fotp-auth&pattern=hexagons&style=style_1&description=Easily+generate+and+verify+an+OTP+using+Laravel+cache&md=2&showWatermark=0&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg" alt="rahmatwaisi-otp-auth">


## Installation

You can install the package with Composer.

```bash
composer require rahmatwaisi/otp-auth
```

## Usage
### Creating an OTP
#### All you need is to use the `OtpGenerator` facade as below:

```php

use RahmatWaisi\OtpAuth\Facades\OtpGenerator;
use RahmatWaisi\OtpAuth\Core\OtpType;
.
.
// To Create a new OTP using a key like User::$id or User::$email, etc.
$otp = OtpGenerator::create('key') // output: 234234
```

#### Another more customizeable way is to determine prefix, key, ttl, length, type, etc.
```php
$user = User::query()->inRandomOrder()->first();

$otp = OtpGenerator::createFrom('smth',  $user->id) // output: 234234, a 6-digit integer

$otp = OtpGenerator::createFrom('smth',  $user->email, OtpType::NUMBER) // output: 234234, a 6-digit integer

$otp = OtpGenerator::createFrom('smth',  $user->username, OtpType::STRING) // output: "aBcDeF", a 6-char string

$otp = OtpGenerator::createFrom('smth',  $user->custom_key, OtpType::NUMBER, 12, now()->addDay()) // output: 123321234234, a 12-digit integer

$otp = OtpGenerator::createFrom('smth',  $user->whatever, OtpType::STRING, 8, now()->addDay()) // output: "aBcdEfgH", an 8-char string

```

> All codes above are using the publishable configs in the package which are located in configs/otp.php

#### Another way that you can customize any configs in the runtime is:

```php
$otp = OtpGenerator::builder()
    ->withPrefix('dummy_prefix')
    ->withKey('my_custom_key')
    ->withType(OtpType::NUMBER)
    ->withTtl(now()->addMinutes(2))
    ->withLength(8)
    ->build();

// Output: 12345678, an 8-digit integer
```


### Resolving, Validating, Removing an OTP
#### All you need is to use the `OtpGenerator` facade as below:

```php

use RahmatWaisi\OtpAuth\Facades\OtpGenerator;
use RahmatWaisi\OtpAuth\Core\OtpType;
.
.
// To Remove, Forget or Resolve an OTP using a key like User::$id or User::$email, etc. Do like this:

// Forgets the OTP just using the key
OtpGenerator::forget($user->id);

// Checks wether an incoming OTP from request exists or not?
OtpGenerator::verify($user->id, request()->get('otp'));
OtpGenerator::verifyUsing('custom_prefix', $user->id, request()->get('otp'));

// Removes an OTP using both key and its value
OtpGenerator::remove($key, 'aBcDeFgH');

// Gets an OTP just using the key
OtpGenerator::get($key);
OtpGenerator::get($user->username);
```

#### Another more customizeable way is to determine prefix, key, ttl, length, type, etc.
```php
$user = User::query()->inRandomOrder()->first();

// Get an OTP just using the key and default configs
OtpGenerator::resolver()->withDefaultSettings()->withKey($user->id)->resolve();

// Forgets an OTP just using the key and default configs
OtpGenerator::resolver()->withDefaultSettings()->withKey($user->id)->forget();

// Checks for existence of an OTP just using the key and default configs
OtpGenerator::resolver()->withDefaultSettings()->withKey($user->id)->exists(request()->get('otp'));

// Get an OTP just using the key and default prefix
OtpGenerator::resolver()->withDefaultPrefix()->withKey($user->id)->resolve();

//  Checks for existence of an OTP just using the key and default perfix
OtpGenerator::resolver()->withDefaultPrefix()->withKey($user->id)->exists(request()->get('otp'));

```

> All codes above are using the publishable configs in the package which are located in configs/otp.php

#### Another way that you can customize any configs in the runtime is:

```php
// Get an OTP just using the key and custom prefix
$otp = OtpGenerator::resolver()
    ->withPrefix('smth')
    ->withKey($user->id)
    ->resolve();
```
 

## Conclusion

By using this package you can easily creat and validate any form with any length of OTPs.


## Contribution
Feel free to suggest improvements, I'll appreciate any contribution from anyone. Thank you.

Made with 💙 In a late late night. 
