<?php

namespace Amot\Conversate;

use Illuminate\Support\Facades\Facade;

class Conversate
{
    private $actions = [];

    public function addAction(string $action, string $class, string $method, $requires_auth = false)
    {
        $this->actions[$action] = [
            "class" => $class,
            "method" => $method,
            "auth" => $requires_auth
        ];
    }

    public function getApiActions(): array
    {
        return $this->actions;
    }

    public function generateToken($id, $ttl = 30)
    {
        $ts = new TokenService();
        return $ts->generateToken($id, $ttl);
    }
}
