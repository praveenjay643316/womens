<?php

namespace App\Core;

/**
 * Small helper for sending HTTP responses: JSON payloads, redirects,
 * and rendered error pages.
 */
class Response
{
    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function redirect(string $url): never
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Render a resources/views/errors/{status}.php page if one exists,
     * otherwise fall back to a plain text message.
     */
    public static function abort(int $status, string $message = ''): never
    {
        http_response_code($status);

        $errorView = VIEWS_PATH . "/errors/{$status}.php";

        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo $message !== '' ? $message : "Error {$status}";
        }

        exit;
    }
}
