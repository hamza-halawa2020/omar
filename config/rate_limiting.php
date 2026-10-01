<?php

return [
    'system' => [
        'enabled' => env('SYSTEM_RATE_LIMIT_ENABLED', true),
        'max_attempts' => (int) env('SYSTEM_RATE_LIMIT_MAX_ATTEMPTS', 120),
        'decay_seconds' => (int) env('SYSTEM_RATE_LIMIT_DECAY_SECONDS', 60),
    ],
];
