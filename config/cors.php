<?php

return [
    'paths' => ['api/*', 'paypal/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:5173',
        'http://localhost:3000',
        'https://trip.smalifahmmed.com',
        'https://trip-api.smalifahmmed.com/',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // Changed to true for JWT
];
