<?php

return [
    'store_id' => env('SSLCOMMERZ_STORE_ID'),
    'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),

    // true = sandbox.sslcommerz.com (test money), false = live
    'sandbox' => env('SSLCOMMERZ_SANDBOX', true),

    'currency' => 'BDT',
];
