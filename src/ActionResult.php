<?php

namespace Amot\Conversate;

class ActionResult
{
    public $id;
    public $client_id;
    public $code;
    public $data;

    public function __construct($id,$client_id,$code, $data)
    {
        $this->id = $id;
        $this->client_id = $client_id;
        $this->code = $code;
        $this->data = $data;
    }

    /**
     * @return string
     * Encodes data back to string to be sent through websocket connection
     */
    public function encode(): string
    {
        return json_encode(["id"=>$this->id,"client_id"=>$this->client_id,"code"=>$this->code,"data"=>$this->data]);
    }
}
