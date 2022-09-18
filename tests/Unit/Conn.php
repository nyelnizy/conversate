<?php


namespace Amot\Conversate\Tests\Unit;


use Amot\Conversate\Event;
use Ratchet\ConnectionInterface;

class Conn implements ConnectionInterface
{
    private $callback;
    function send($data)
    {
        $this->invokeCallback($data,$this->callback);

    }

    function close()
    {
        // TODO: Implement close() method.
    }
    public function receiveData($callback){
        $this->callback = $callback;
    }
    private function invokeCallback($data,$func){
        $func($data);
    }
}