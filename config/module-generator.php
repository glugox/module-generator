<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module Generator Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for the module generator package.
    | This includes paths, namespaces, and other options relevant to module
    | generation within your Laravel application.
    |
    */

    'module_path' => base_path('modules'),

    'namespace' => 'App\\Modules',

    'stubs_path' => dirname(__DIR__) . '/stubs',

    'spec' => [
        'rules' => [],
    ],
];
