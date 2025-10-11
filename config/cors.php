<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:*',
        'https://localhost:*',
        'https://*.exp.direct',
        'https://auth.expo.dev',
        'https://adminlt.tungocvan.com',
        'https://esxv7iq-tungocvan-8081.exp.direct'
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
