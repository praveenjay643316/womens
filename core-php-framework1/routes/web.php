<?php

/**
 * Web routes.
 *
 * $router is provided by bootstrap/app.php before this file is included.
 *
 * @var \App\Core\Router $router
 */

use App\Controllers\UserController;
use App\Middleware\VerifyCsrfToken;

$router->get('/', [UserController::class, 'index']);

$router->get('/users', [UserController::class, 'index']);
$router->get('/users/create', [UserController::class, 'create']);
$router->post('/users', [UserController::class, 'store'], [VerifyCsrfToken::class]);
$router->get('/users/{id}/edit', [UserController::class, 'edit']);
$router->put('/users/{id}', [UserController::class, 'update'], [VerifyCsrfToken::class]);
$router->delete('/users/{id}', [UserController::class, 'destroy'], [VerifyCsrfToken::class]);
