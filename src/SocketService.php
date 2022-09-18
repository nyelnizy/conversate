<?php

namespace Amot\Conversate;

use Amot\Conversate\RequestConsumer;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class SocketService implements MessageComponentInterface
{
    protected $clients;
    protected $queue;

    /**
     * SocketService constructor.
     * @var $queue
     * The request queue object stores all incoming
     * requests to be processed in the order received
     * @var $clients
     * The clients object storage stores all connected client connections
     * @RequestConsumer
     * This class initializes a listener (the queue consumer) that gets notified on every request
     */
    public function __construct()
    {
        $this->queue = $GLOBALS['api_queue'] = new \Ds\Queue();
        $this->clients = new \SplObjectStorage;
        new RequestConsumer();
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onOpen(ConnectionInterface $conn)
    {
        // requests are received on the path /client_id
        $client_id = str_replace('/', '', $conn->httpRequest->getUri()->getPath());
        // create a client object representing the currently connected client
        $client = new \stdClass();
        $client->channel = $client_id;
        $client->conn = $conn;
        $this->clients->attach($client);
        Logger::log("New connection! - ({$conn->resourceId})");
    }

    /**
     * @param ConnectionInterface $from
     * @param $msg
     * @return void
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        // push request to queue and signal listener/consumer
        $this->queue->push(new RequestPacket($msg, $from));
        Event::signal('new-request');
    }

    /**
     * @param ConnectionInterface $conn
     * @param \Exception $e
     * @return void
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Logger::log("An error has occurred: {$e->getMessage()}");
        $conn->close();
    }

    /**
     * @param ConnectionInterface $conn
     * @return void
     */
    public function onClose(ConnectionInterface $conn)
    {
        $cl = $this->getClient($conn->resourceId);
        if ($cl) {
            $this->clients->detach($cl);
        }
        Logger::log("Connection {$conn->resourceId} has disconnected");
    }

    /**
     * @param $id
     * @return mixed|object|null
     * Gets a client instance to talk to from the client id
     */
    private function getClient($id)
    {
        $cl = null;
        foreach ($this->clients as $client) {
            if ($client->conn->resourceId === $id) {
                $cl = $client;
                break;
            }
        }
        return $cl;
    }

}
