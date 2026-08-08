<?php

use App\Controllers\HealthController;
use App\Core\Router;
use App\Views\JsonView;
use App\Controllers\AuthController;
use App\Core\Database;
use App\Repositories\PdoApiTokenRepository;
use App\Repositories\PdoMerchantRepository;
use App\Services\AuthService;
use App\Auth\BearerAuthenticator;
use App\Controllers\PaymentController;
use App\Payments\FakeStripeProvider;
use App\Payments\PaymentProviderResolver;
use App\Repositories\PdoTransactionRepository;
use App\Services\PaymentService;
use App\Controllers\TransactionController;
use App\Payments\CardValidator;

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
$transactionRepository = new PdoTransactionRepository($connection);
$cardValidator = new CardValidator();

$authService = new AuthService(
    $merchantRepository,
    $apiTokenRepository
);

$bearerAuthenticator = new BearerAuthenticator($authService);

$paymentProviderResolver = new PaymentProviderResolver([
    'fake_stripe' => new FakeStripeProvider(),
]);

$paymentService = new PaymentService(
    $transactionRepository,
    $paymentProviderResolver
);

$healthController = new HealthController($view);
$authController = new AuthController($authService, $view);
$paymentController = new PaymentController(
    $paymentService,
    $bearerAuthenticator,
    $view,
    $cardValidator
);

$transactionController = new TransactionController(
    $transactionRepository,
    $bearerAuthenticator,
    $view
);

$router->get('/', [$healthController, 'index']);

$router->post('/api/auth/login', [
    $authController,
    'login'
]);

$router->post('/api/payments', [
    $paymentController,
    'charge'
]);

$router->get('/api/transactions', [
    $transactionController,
    'index'
]);

$router->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);
