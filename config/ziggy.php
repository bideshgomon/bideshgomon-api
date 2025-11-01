<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Include Filters
    |--------------------------------------------------------------------------
    |
    | A list of filters for including routes in the output. Can be route
    | names, route name patterns, or middleware names.
    |
    */

    'groups' => [
        'web', // <-- This is the line we added. It scans routes/web.php
        'api', // We should also add API routes
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclude Filters
    |--------------------------------------------------------------------------
    |
    | A list of filters for excluding routes from the output. Can be route
    | names, route name patterns, or middleware names.
    |
    */

    'except' => [
        'debugbar.*',
        'ignition.*',
        '_ignition.*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Output Path
    |--------------------------------------------------------------------------
    |
    | The path to the file Ziggy will create with your routes.
    |
    */

    'path' => 'resources/js/ziggy.js',

    /*
    |--------------------------------------------------------------------------
    | Skip Route Function
    |--------------------------------------------------------------------------
    |
    | A closure that receives a route and should return true if the route
    | should be skipped.
    |
    */

    'skip-route-function' => null,

    /*
    |--------------------------------------------------------------------------
    | Output Format
    |--------------------------------------------------------------------------
    |
    | The format of the output file. Can be 'js' or 'json'.
    |
    */

    'output' => [
        'format' => 'js',
    ],

    /*
    |--------------------------------------------------------------------------
    | "Only"
    |--------------------------------------------------------------------------
    |
    | Only include routes matching the given patterns.
    |
    */

    // 'only' => [], // We are using 'groups' instead

    /*
    |--------------------------------------------------------------------------
    | "Except"
    |--------------------------------------------------------------------------
    |
    | Exclude routes matching the given patterns.
    |
    */

    // 'except' => [], // We are using the main 'except' array

    /*
    |--------------------------------------------------------------------------
    | URL
    |--------------------------------------------------------------------------
    |
    | The URL your application is served from.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Output File Path
    |--------------------------------------------------------------------------
    |
    | The path to the file Ziggy will create with your routes.
    |
    */

    // 'path' => 'resources/js/ziggy.js', // Redundant, but fine

    /*
    |--------------------------------------------------------------------------
    | Built-in Routes
    |--------------------------------------------------------------------------
    |
    | Whether to include routes defined by Laravel (e.g. auth routes).
    |
    */

    'include_built_in_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | The cache file to use for Ziggy's route list.
    |
    */

    'cache' => [
        'path' => storage_path('framework/cache/ziggy.json'),
        'enabled' => env('ZIGGY_CACHE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    |
    | How to sort the routes in the output file.
    |
    */

    'sorting' => 'name',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | The middleware to use for Ziggy's routes endpoint.
    |
    */

    'middleware' => 'web',
];
