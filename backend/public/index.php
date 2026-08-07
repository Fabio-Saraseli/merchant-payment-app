<?php

use App\Controllers\HealthController;
use App\Core\Router;
use App\Views\JsonView;
use App\Controllers\AuthController;
use App\Core\Database;
use App\Repositories\PdoApiTokenRepository;
use App\Repositories\PdoMerchantRepository;
use App\Services\AuthService;

require dirname(__DIR__) . '/vendor/autoload.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = new Router();
$view = new JsonView();

$connection = (new Database())->connect();

$merchantRepository = new PdoMerchantRepository($connection);
$apiTokenRepository = new PdoApiTokenRepository($connection);

$authService = new AuthService(
    $merchantRepository,
    $apiTokenRepository
);

$healthController = new HealthController($view);
$authController = new AuthController($authService, $view);

$router->get('/', [$healthController, 'index']);

$router->post('/api/auth/login', [
    $authController,
    'login'
]);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
