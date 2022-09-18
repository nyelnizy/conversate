<?php

namespace Amot\Conversate;

class RequestConsumer
{
    public function __construct()
    {
        $this->consumePackets();
    }

    /**
     * @param RequestPacket $packet
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function dispatch(RequestPacket $packet)
    {
        // create a request object from $packet
        $request = new Request($packet->payload);
        Logger::log("ACTION:-> " . $request->action);
        Logger::log("CLIENT ID:-> " . $request->client_id);

        // get an instance of action result by invoking complete
        $result = $request->complete();
        switch ($request->validate()) {
            case 0:
                $result->code = 404;
                $result->data = "Action not found";
                break;
            case 1:
                $result = $request->execute();
                break;
            case 2:
                $result->code = 401;
                $result->data = "Invalid Token";
        }
        $packet->conn->send($result->encode());
    }

    private function consumePackets()
    {
        Event::listen('new-request', function (RequestPacket $packet) {
            $this->dispatch($packet);
        });
    }
}
