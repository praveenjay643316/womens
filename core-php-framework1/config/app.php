<?php

/**
 * Application configuration.
 *
 * All values are pulled from the environment (.env) with sensible defaults.
 */
return [
    'name'     => env('APP_NAME', 'Core PHP Framework'),
    'env'      => env('APP_ENV', 'local'),
    'debug'    => env('APP_DEBUG', true),
    'url'      => env('APP_URL', 'http://localhost:8000'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Kolkata'),
];
