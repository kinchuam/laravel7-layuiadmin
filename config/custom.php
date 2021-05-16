<?php
return [
    'article_tier' => env('ARTICLE_TIER', 2),

    'config_cache_time' => env('CONFIG_CACHE_TIME', 120),

    'operation_log' => [
        'enable' => true,
        'except' => [],
    ],
];
