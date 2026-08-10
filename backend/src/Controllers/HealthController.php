<?php

namespace App\Controllers;

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
