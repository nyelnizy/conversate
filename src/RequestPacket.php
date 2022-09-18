<?php

namespace Amot\Conversate;

use Ratchet\ConnectionInterface;

class RequestPacket
{
    public $payload;
    public $conn;
    public function __construct(string $payload,ConnectionInterface $conn)
    {
        $this->payload = $payload;
        $this->conn = $conn;
    }
}
