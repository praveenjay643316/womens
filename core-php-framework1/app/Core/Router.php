<?php

namespace App\Core;

/**
 * Simple regex-based router.
 *
 * Supports GET/POST/PUT/DELETE, {param} route segments, and an optional
 * per-route middleware pipeline:
 *
 *   $router->get('/users/{id}/edit', [UserController::class, 'edit']);
 *   $router->post('/users', [UserController::class, 'store'], [VerifyCsrfToken::class]);
 *
 * PUT/DELETE are reached via method spoofing (a hidden "_method" field
 * or a POST with X-HTTP-Method-Override), see Request::method().
 */
class Router
{
    /** @var array<string, array<string, array{action: array, middleware: array}>> */
    protected array $routes = [];

    public function get(string $uri, array $action, array $middleware = []): void
    {
        $this->addRoute('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, array $action, array $middleware = []): void
    {
        $this->addRoute('POST', $uri, $action, $middleware);
    }

    public function put(string $uri, array $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $uri, $action, $middleware);
    }

    public function delete(string $uri, array $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $uri, $action, $middleware);
    }

    protected function addRoute(string $method, string $uri, array $action, array $middleware): void
    {
        $uri = $uri === '/' ? '/' : rtrim($uri, '/');

        $this->routes[$method][$uri] = [
            'action'     => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri    = $request->uri();

        foreach ($this->routes[$method] ?? [] as $routeUri => $route) {
            $pattern = $this->toRegex($routeUri, $paramNames);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $params = array_combine($paramNames, $matches);
                $request->setParams($params);

                $this->runMiddleware(
                    $route['middleware'],
                    $request,
                    function (Request $request) use ($route, $params): void {
                        $this->callAction($route['action'], $request, $params);
                    }
                );

                return;
            }
        }

        Response::abort(404, 'Page Not Found');
    }

    protected function toRegex(string $uri, ?array &$paramNames): string
    {
        $paramNames = [];

        $pattern = preg_replace_callback(
            '#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#',
            function (array $m) use (&$paramNames): string {
                $paramNames[] = $m[1];
                return '([^/]+)';
            },
            $uri
        );

        return '#^' . $pattern . '$#';
    }

    /**
     * Build and run the middleware pipeline for a matched route, ending
     * with $destination (the actual controller call).
     *
     * @param class-string[] $middleware
     */
    protected function runMiddleware(array $middleware, Request $request, callable $destination): void
    {
        $pipeline = array_reduce(
            array_reverse($middleware),
            function (callable $next, string $middlewareClass): callable {
                return function (Request $request) use ($middlewareClass, $next): mixed {
                    /** @var MiddlewareInterface $instance */
                    $instance = new $middlewareClass();
                    return $instance->handle($request, $next);
                };
            },
            $destination
        );

        $pipeline($request);
    }

    protected function callAction(array $action, Request $request, array $params): void
    {
        [$class, $method] = $action;

        $controller = new $class();
        $output     = call_user_func_array([$controller, $method], [$request, ...array_values($params)]);

        if (is_string($output)) {
            echo $output;
        }
    }
}
