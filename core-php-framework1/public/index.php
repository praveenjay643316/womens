<?php

/**
 * Front controller.
 *
 * Every HTTP request is routed through this single file
 * (see composer serve -> php -S localhost:8000 -t public).
 */

define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('VIEWS_PATH', BASE_PATH . '/resources/views');
define('STORAGE_PATH', BASE_PATH . '/storage');

$app = require BASE_PATH . '/bootstrap/app.php';
$app->run();
