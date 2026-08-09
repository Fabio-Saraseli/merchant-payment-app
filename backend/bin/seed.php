<?php

use App\Core\Database;
use App\Models\Merchant;
use App\Repositories\PdoMerchantRepository;

require dirname(__DIR__) . '/vendor/autoload.php';

$connection = (new Database())->connect();
$merchantRepository = new PdoMerchantRepository($connection);

$merchants = [
    [
        'name' => 'Demo Merchant',
        'email' => 'demo@merchant.test',
        'password' => 'password123',
        'payment_provider' => 'fake_stripe',
        'payment_provider_config' => [
            'account_id' => 'fake_account_demo',
        ],
    ],
    [
        'name' => 'Second Merchant',
        'email' => 'second@merchant.test',
        'password' => 'password123',
        'payment_provider' => 'fake_stripe',
        'payment_provider_config' => [
            'account_id' => 'fake_account_second',
        ],
    ],
];

foreach ($merchants as $merchantData) {
    $existingMerchant = $merchantRepository->findByEmail(
        $merchantData['email']
    );

    if ($existingMerchant) {
        echo $merchantData['name'] . " already exists." . PHP_EOL;
        continue;
    }

    $passwordHash = password_hash(
        $merchantData['password'],
        PASSWORD_DEFAULT
    );

    if (!$passwordHash) {
        throw new RuntimeException(
            'Could not hash the merchant password.'
        );
    }

    $merchant = new Merchant(
        null,
        $merchantData['name'],
        $merchantData['email'],
        $passwordHash,
        $merchantData['payment_provider'],
        $merchantData['payment_provider_config']
    );

    $merchantRepository->create($merchant);

    echo $merchantData['name'] . " created." . PHP_EOL;
}
