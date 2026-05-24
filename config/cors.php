<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['https://karsindofurniture.co.id', 'https://cms.karsindofurniture.co.id', 'https://karsindo.vercel.app', 'https://cms-karsindo.vercel.app', 'http://localhost:3000', 'http://localhost:3001'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
