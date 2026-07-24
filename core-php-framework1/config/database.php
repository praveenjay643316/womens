<?php

/**
 * Database connection configuration.
 *
 * All values are pulled from the environment (.env) with sensible defaults.
 */
return [
    'connection' => env('DB_CONNECTION', 'pgsql'),
    'host'       => env('DB_HOST', '127.0.0.1'),
    'port'       => env('DB_PORT', '5432'),
    'database'   => env('DB_DATABASE', ''),
    'username'   => env('DB_USERNAME', 'postgres'),
    'password'   => env('DB_PASSWORD', ''),
];
