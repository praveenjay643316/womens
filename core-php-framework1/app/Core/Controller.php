<?php

namespace App\Core;

/**
 * Base controller. Application controllers extend this for convenient
 * access to view rendering, redirects, and JSON responses.
 */
abstract class Controller
{
    protected function view(string $view, array $data = []): string
    {
        return View::render($view, $data);
    }

    protected function redirect(string $url): never
    {
        Response::redirect($url);
    }

    protected function json(array $data, int $status = 200): never
    {
        Response::json($data, $status);
    }
}
