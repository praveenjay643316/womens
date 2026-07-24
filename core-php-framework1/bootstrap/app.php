<?php

/**
 * Application bootstrap.
 *
 * Loads the Composer autoloader, environment variables, timezone/error
 * settings, then registers routes and returns a ready-to-run App instance.
 *
 * Expects BASE_PATH, CONFIG_PATH, VIEWS_PATH and STORAGE_PATH to already be
 * defined by the front controller (public/index.php).
 */

use App\Core\App;
use App\Core\Env;
use App\Core\Router;

require BASE_PATH . '/vendor/autoload.php';

// Load .env into $_ENV / getenv() before anything else touches env()/config().
(new Env(BASE_PATH . '/.env'))->load();

$debug = env('APP_DEBUG', true);

if ($debug) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Kolkata'));

$router = new Router();
require BASE_PATH . '/routes/web.php';

return new App($router);
