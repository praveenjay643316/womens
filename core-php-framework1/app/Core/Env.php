<?php

namespace App\Core;

/**
 * Minimal .env file loader.
 *
 * Deliberately dependency-free (no vlucas/phpdotenv) so the framework has
 * zero third-party packages and `composer install` works fully offline.
 */
class Env
{
    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Parse the .env file and populate $_ENV / getenv().
     * Existing environment variables are never overwritten.
     */
    public function load(): void
    {
        if (!file_exists($this->path)) {
            return;
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name  = trim($name);
            $value = trim($value);
            $value = trim($value, '"\'');

            if (!array_key_exists($name, $_ENV)) {
                $_ENV[$name] = $value;
                putenv("{$name}={$value}");
            }
        }
    }
}
