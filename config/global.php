<?php

return [
    'offline_asset' => env('OFFLINE_ASSET', true),
    'assets_version' => env('ASSETS_VERSION', time()),
    'allow_asset_version' => env('ALLOW_ASSET_VERSION', false),
    'scripts' => [
        'tailwind.js',
    ],
    'styles' => [
        'tailwind.css',
    ],
];
