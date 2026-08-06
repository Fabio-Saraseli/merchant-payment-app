<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Views\JsonView;

class HealthController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function index()
    {
        $this->view->render([
            'status' => 'ok',
            'service' => 'merchant-payment-api',
        ]);
    }
}