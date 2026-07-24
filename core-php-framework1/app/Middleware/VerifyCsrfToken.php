<?php

namespace App\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;

/**
 * Rejects any state-changing request (POST/PUT/DELETE) whose "_token"
 * field doesn't match the token stored in the session. Attach to routes
 * via the $middleware argument, e.g.:
 *
 *   $router->post('/users', [UserController::class, 'store'], [VerifyCsrfToken::class]);
 */
class VerifyCsrfToken implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): mixed
    {
        if (in_array($request->method(), ['POST', 'PUT', 'DELETE'], true)) {
            $token = $request->input('_token');

            if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', (string) $token)) {
                Response::abort(419, 'Page Expired: invalid or missing CSRF token.');
            }
        }

        return $next($request);
    }
}
