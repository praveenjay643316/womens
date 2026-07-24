<?php

namespace App\Core;

/**
 * Thin wrapper around PHP sessions that also implements one-request
 * "flash" data for validation errors and old input, similar in spirit
 * to Laravel's session flash mechanism.
 */
class Session
{
    protected static array $flash = [
        'old'    => [],
        'errors' => [],
    ];

    /**
     * Start the native session and pull any flashed old input / errors
     * out of it so they are available for exactly one request.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        self::$flash['old']    = $_SESSION['old'] ?? [];
        self::$flash['errors'] = $_SESSION['errors'] ?? [];

        unset($_SESSION['old'], $_SESSION['errors']);
    }

    public static function old(string $key, mixed $default = ''): mixed
    {
        return self::$flash['old'][$key] ?? $default;
    }

    public static function errors(?string $key = null): mixed
    {
        if ($key !== null) {
            return self::$flash['errors'][$key] ?? [];
        }

        return self::$flash['errors'];
    }

    public static function hasErrors(): bool
    {
        return !empty(self::$flash['errors']);
    }
}
