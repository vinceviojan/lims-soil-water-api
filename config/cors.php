<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'fert', '*'], // routes to allow CORS

    'allowed_methods' => ['*'], // allow all methods: GET, POST, PUT, DELETE, OPTIONS

    // 'allowed_origins' => ['http://127.0.0.1:5500'], // your frontend origin

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // allow all headers

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
