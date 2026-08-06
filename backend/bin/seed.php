<?php

use App\Core\Database;
use App\Models\Merchant;
use App\Repositories\PdoMerchantRepository;

require dirname(__DIR__) . '/vendor/autoload.php';

$connection = (new Database())->connect();
$merchantRepository = new PdoMerchantRepository($connection);

$email = 'demo@merchant.test';

$existingMerchant = $merchantRepository->findByEmail($email);

if ($existingMerchant) {
    echo "Demo merchant already exists." . PHP_EOL;
    exit;
}

$passwordHash = password_hash('password123', PASSWORD_DEFAULT);

if (!$passwordHash) {
    throw new RuntimeException('Could not hash the merchant password.');
}

$merchant = new Merchant(
    null,
    'Demo Merchant',
    $email,
    $passwordHash,
    'fake_stripe'
);

$merchantRepository->create($merchant);

echo "Demo merchant created." . PHP_EOL;
