<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active gateway
    |--------------------------------------------------------------------------
    |
    | Swap this one key to change provider. `fake` settles instantly and never
    | touches the network, which is what local development and the test suite
    | use; `zarinpal` performs a real redirect and verification.
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'fake'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | Amounts are stored in Toman throughout the application, because that is
    | what the board and applicants actually deal in. Each driver converts at
    | its own boundary if the provider expects Rial.
    |
    */

    'currency' => env('PAYMENT_CURRENCY', 'IRT'),

    'gateways' => [

        'fake' => [
            'driver' => 'fake',
        ],

        'zarinpal' => [
            'driver' => 'zarinpal',
            'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
            'sandbox' => env('ZARINPAL_SANDBOX', true),
        ],

    ],

];
