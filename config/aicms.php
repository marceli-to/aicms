<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Editable Paths
    |--------------------------------------------------------------------------
    |
    | Define which paths/files the AI can read and edit. Paths are relative
    | to the base_path(). Use glob patterns for flexibility.
    |
    */
    'editable_paths' => [
        'resources/views/pages/*.blade.php',
        'content/**/*.md',
    ],

    /*
    |--------------------------------------------------------------------------
    | Anthropic API
    |--------------------------------------------------------------------------
    */
    'anthropic_key' => env('AICMS_ANTHROPIC_KEY'),
    'model' => env('AICMS_MODEL', 'claude-sonnet-4-20250514'),

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    */
    'route_prefix' => 'aicms',
    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | UI Settings
    |--------------------------------------------------------------------------
    */
    'title' => 'AI CMS',
];
