<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active gateway
    |--------------------------------------------------------------------------
    |
    | Swap this one key to change provider. `log` writes the message to the log
    | and never touches the network, which is what local development and the test
    | suite use; `kavenegar` sends for real.
    |
    */

    'default' => env('SMS_GATEWAY', 'log'),

    'log_channel' => env('SMS_LOG_CHANNEL', 'single'),

    /*
    |--------------------------------------------------------------------------
    | Showing codes on screen
    |--------------------------------------------------------------------------
    |
    | With the log driver there is no phone to read the code from, so the
    | verification page prints it instead. This is the sign-in equivalent of the
    | fake payment gateway and must never be true anywhere real — it hands a
    | working credential to whoever loads the page.
    |
    */

    'expose_codes' => env('SMS_EXPOSE_CODES', env('APP_ENV') === 'local'),

    /*
    |--------------------------------------------------------------------------
    | One-time codes
    |--------------------------------------------------------------------------
    |
    | `ttl` is how long a code stays valid, `resend_after` how long a member must
    | wait before asking for another, and `max_attempts` how many wrong guesses a
    | single code tolerates before it is burned. Six digits over three minutes
    | with five tries leaves roughly a one-in-200,000 chance per code.
    |
    */

    'code' => [
        'length' => 6,
        'ttl' => 180,             // seconds
        'resend_after' => 90,     // seconds
        'max_attempts' => 5,
        'max_per_hour' => 5,      // codes issued to one number per hour
    ],

    'gateways' => [

        'log' => [
            'driver' => 'log',
        ],

        'kavenegar' => [
            'driver' => 'kavenegar',
            'api_key' => env('KAVENEGAR_API_KEY'),
            'sender' => env('KAVENEGAR_SENDER'),
            'template' => env('KAVENEGAR_OTP_TEMPLATE', 'judo-login'),
        ],

    ],

];
