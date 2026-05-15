<?php

declare(strict_types=1);

return [
    'base_url' => env('PETSTORE_BASE_URL', 'https://petstore.swagger.io/v2'),
    'api_key' => env('PETSTORE_API_KEY', 'special-key'), // not really secret, since it's a public API, but let's keep it in env for consistency
    'timeout' => (int) env('PETSTORE_TIMEOUT', 10),
    'retry' => (int) env('PETSTORE_RETRY', 2),
    'cache_ttl' => (int) env('PETSTORE_CACHE_TTL', 300),
];
