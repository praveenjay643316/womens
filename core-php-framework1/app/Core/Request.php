<?php

namespace App\Core;

/**
 * Wraps the incoming HTTP request: query/body params, uploaded files,
 * route parameters, and method spoofing for PUT/DELETE via a hidden
 * "_method" field (since native HTML forms only support GET/POST).
 */
class Request
{
    protected array $query;
    protected array $body;
    protected array $server;
    protected array $files;
    protected array $params = [];

    public function __construct()
    {
        $this->query  = $_GET;
        $this->body   = $_POST;
        $this->server = $_SERVER;
        $this->files  = $_FILES;
    }

    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');

        if ($method === 'POST' && !empty($this->body['_method'])) {
            $method = strtoupper((string) $this->body['_method']);
        }

        return $method;
    }

    public function uri(): string
    {
        $uri = parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function only(array $keys): array
    {
        return array_intersect_key($this->all(), array_flip($keys));
    }

    public function except(array $keys): array
    {
        return array_diff_key($this->all(), array_flip($keys));
    }

    public function has(string $key): bool
    {
        return isset($this->body[$key]) || isset($this->query[$key]);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Validate the request against Laravel-style pipe rules, e.g.
     * ['email' => 'required|email|unique:users,email']
     *
     * On failure this redirects back with old input + errors flashed
     * to the session and does not return (see Validator::validate()).
     */
    public function validate(array $rules): array
    {
        return Validator::validate($this->all(), $rules);
    }
}
