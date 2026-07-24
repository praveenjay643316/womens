<?php

namespace App\Core;

/**
 * Contract for route middleware. Implementations receive the current
 * Request and a $next closure that continues the pipeline — call it to
 * proceed, or short-circuit (e.g. Response::abort()) to stop the request.
 */
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): mixed;
}
