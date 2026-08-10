<?php

namespace App\Http;

class Request
{
    public function json()
    {
        return json_decode(
            file_get_contents('php://input'),
            true
        );
    }

    public function query($name)
    {
        return $_GET[$name] ?? null;
    }
}