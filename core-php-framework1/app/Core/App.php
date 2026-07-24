<?php

namespace App\Core;

/**
 * Application kernel: starts the session, dispatches the request through
 * the router, and converts uncaught exceptions into a 500 page (logging
 * the real error to storage/logs/app.log rather than leaking it to users
 * unless APP_DEBUG is enabled).
 */
class App
{
    public function __construct(protected Router $router)
    {
    }

    public function run(): void
    {
        Session::start();

        try {
            $request = new Request();
            $this->router->dispatch($request);
        } catch (\Throwable $e) {
            Logger::error($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

            if (env('APP_DEBUG', false)) {
                throw $e;
            }

            Response::abort(500, 'Internal Server Error');
        }
    }
}
