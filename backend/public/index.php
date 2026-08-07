<?php

use App\Controllers\HealthController;
use App\Core\Router;
use App\Views\JsonView;

require dirname(__DIR__) . '/vendor/autoload.php';

$router = new Router();

$healthController = new HealthController(new JsonView());

$router->get('/', [$healthController, 'index']);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
