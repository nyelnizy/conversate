<?php

namespace Amot\Conversate;
/**
 * @property string $id
 * @property string $client_id
 * @property string $action
 * @property mixed $parameter
 * @property string $token
 * @property $user_id
 */
class Request
{
    public $id;
    public $client_id;
    public $action;
    public $parameter;
    public $token;
    public $user_id;

    private $requestAction;

    /**
     * @param string $payload
     */
    public function __construct(string $payload)
    {
        $decoded_payload = json_decode($payload, true);
        $this->id = $decoded_payload["id"];
        $this->client_id = $decoded_payload["client_id"];
        $this->action = $decoded_payload["action"];
        $this->token = $decoded_payload["token"];
        $this->parameter = $decoded_payload["parameter"];
    }

    /**
     * @param string $data
     * @param int $code
     * @return ActionResult
     */
    public function complete($data = "Completed Successfully", $code = 200): ActionResult
    {
        return new ActionResult($this->id, $this->client_id, $code, $data);
    }

    /**
     * @return int
     * 0 Action Not Found
     * 1 Validated Successfully
     * 2 Invalid Token
     */
    public function validate(): int
    {
        $actions = \Amot\Conversate\Facades\Conversate::getApiActions();

        if (!array_key_exists($this->action, $actions)) {
            return 0;
        }
        $this->requestAction = $actions[$this->action];
        if (empty($this->requestAction)) {
            return 0;
        }
        // validate
        if ($this->requestAction["auth"]) {
            if (!$this->validateToken($this->token)) {
                return 2;
            }
        }
        return 1;
    }

    /**
     * @return mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function execute(): ActionResult
    {
        $class = app()->make($this->requestAction["class"]);
        return $class->{$this->requestAction["method"]}($this);
    }

    /**
     * @param $token
     * @return bool
     */
    private function validateToken($token): bool
    {
        $ts = new TokenService();
        $id = $ts->verifyToken($token);
        if (is_null($id)) {
            return false;
        }
        $this->user_id = $id;
        return true;
    }
}
