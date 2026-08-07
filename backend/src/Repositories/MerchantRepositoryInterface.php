<?php

namespace App\Repositories;

interface MerchantRepositoryInterface
{
    public function findByEmail($email);
    public function create($merchant);
    public function findById($id);
}
