<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Server Key
    |--------------------------------------------------------------------------
    */

    'server_key' => env('MIDTRANS_SERVER_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Client Key
    |--------------------------------------------------------------------------
    */

    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Merchant ID
    |--------------------------------------------------------------------------
    */

    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Is Production
    |--------------------------------------------------------------------------
    | Set to true for production environment, false for sandbox.
    */

    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Snap API URL
    |--------------------------------------------------------------------------
    */

    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/v1/transactions'
        : 'https://app.sandbox.midtrans.com/snap/v1/transactions',

    /*
    |--------------------------------------------------------------------------
    | API URL (for status, cancel, etc.)
    |--------------------------------------------------------------------------
    */

    'api_url' => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://api.midtrans.com/v2'
        : 'https://api.sandbox.midtrans.com/v2',

    /*
    |--------------------------------------------------------------------------
    | Callback URL
    |--------------------------------------------------------------------------
    */

    'finish_redirect_url' => env('APP_URL').'/marketplace/success',
    'unfinish_redirect_url' => env('APP_URL').'/marketplace',
    'error_redirect_url' => env('APP_URL').'/marketplace',

    /*
    |--------------------------------------------------------------------------
    | Platform Fee Percentage
    |--------------------------------------------------------------------------
    | Percentage taken by platform from each marketplace transaction.
    */

    'platform_fee_percentage' => env('MIDTRANS_PLATFORM_FEE_PERCENTAGE', 20),

];
