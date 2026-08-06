<?php

declare(strict_types=1);

use App\Controllers\HealthController;
use App\Views\JsonView;

require dirname(__DIR__) . '/vendor/autoload.php';

$view = new JsonView();
$controller = new HealthController($view);

$controller->index();