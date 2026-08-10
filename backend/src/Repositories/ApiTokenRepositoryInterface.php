<?php

namespace App\Repositories;

interface ApiTokenRepositoryInterface
{
    public function create($apiToken);

    public function findByTokenHash($tokenHash);
}
