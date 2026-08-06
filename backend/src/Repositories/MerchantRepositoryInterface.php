<?php

namespace App\Repositories;

interface MerchantRepositoryInterface
{
    public function findByEmail($email);
}
