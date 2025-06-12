<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Linode API Key
    |--------------------------------------------------------------------------
    |
    | This is the API key used to authenticate with the Linode API.
    | You can generate a key from your Linode account dashboard.
    |
    */
    'api_key' => env('LINODE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | The version of the Linode API to use.
    |
    */
    'api_version' => 'v4',

    /*
    |--------------------------------------------------------------------------
    | API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL for the Linode API.
    |
    */
    'api_url' => 'https://api.linode.com',

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout for API requests in seconds.
    |
    */
    'timeout' => 30,
];
