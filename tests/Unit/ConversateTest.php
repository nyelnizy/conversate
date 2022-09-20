<?php

namespace Amot\Conversate\Tests\Unit;

use Amot\Conversate\Event;
use Amot\Conversate\RequestConsumer;
use Amot\Conversate\RequestPacket;
use Amot\Conversate\TokenService;
use Mockery\MockInterface;
use function PHPUnit\Framework\assertTrue;

class ConversateTest extends \Orchestra\Testbench\TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_add_action()
    {
        \Amot\Conversate\Facades\Conversate::addAction("get-users", SampleUserService::class, "getUsers");
        $actions = \Amot\Conversate\Facades\Conversate::getApiActions();
        self::assertTrue(!empty($actions["get-users"]));
    }

    public function test_request_events()
    {
        $queue = $GLOBALS['api_queue'] = new \Ds\Queue();
        Event::listen('new-request', function (RequestPacket $packet) {
            assertTrue(!is_null($packet->conn));
            assertTrue($packet->payload === "Hello");
        });
        $queue->push(new RequestPacket("Hello", new FakeConn()));
        Event::signal("new-request");
    }

    public function test_received_request_is_processed_if_action_is_valid()
    {
        $queue = $GLOBALS['api_queue'] = new \Ds\Queue();
        \Amot\Conversate\Facades\Conversate::addAction("get-users", SampleUserService::class, "getUsers");
        new RequestConsumer();
        $conn = new FakeConn();
        $conn->receiveData(function ($data){
            $d = json_decode($data)->data;
            assertTrue(count($d)===2);
            assertTrue($d[0]->gender==="male");
        });
        $queue->push(new RequestPacket(json_encode(
            [
                "id" => 1,
                "client_id" => 3,
                "action" => "get-users",
                "token" => "xxx",
                "parameter" => "male"
            ]
        ), $conn));
        Event::signal("new-request");
    }

    public function test_received_request_is_rejected_if_action_is_not_found()
    {
        $queue = $GLOBALS['api_queue'] = new \Ds\Queue();
        \Amot\Conversate\Facades\Conversate::addAction("get-users", SampleUserService::class, "getUsers");
        new RequestConsumer();
        $conn = new FakeConn();
        $conn->receiveData(function ($data){
            $d = json_decode($data)->code;
            assertTrue($d===404);
        });
        $queue->push(new RequestPacket(json_encode(
            [
                "id" => 1,
                "client_id" => 3,
                "action" => "get-user",
                "token" => "xxx",
                "parameter" => "male"
            ]
        ), $conn));
        Event::signal("new-request");
    }
    public function test_request_with_no_token_is_rejected_if_action_requires_auth()
    {
        $queue = $GLOBALS['api_queue'] = new \Ds\Queue();
        \Amot\Conversate\Facades\Conversate::addAction("get-users", SampleUserService::class, "getUsers",true);
        new RequestConsumer();
        $conn = new FakeConn();
        $conn->receiveData(function ($data){
            $d = json_decode($data)->code;
            assertTrue($d===401);
        });
        $queue->push(new RequestPacket(json_encode(
            [
                "id" => 1,
                "client_id" => 3,
                "action" => "get-users",
                "token" => "xxx",
                "parameter" => "male"
            ]
        ), $conn));
        Event::signal("new-request");
    }
    public function test_generate_token_returns_valid_token()
    {
        $ts = new TokenService();
        $token = $ts->generateToken(1);
        $this->assertNotNull($token);
        $id = $ts->verifyToken($token);
        $this->assertTrue(intval($id)===1);
    }
    public function test_request_with_token_is_processed_if_action_requires_auth()
    {
        $ts = new TokenService();
        $token = $ts->generateToken(1);
        $queue = $GLOBALS['api_queue'] = new \Ds\Queue();
        \Amot\Conversate\Facades\Conversate::addAction("get-users", SampleUserService::class, "getUsers",true);
        new RequestConsumer();
        $conn = new FakeConn();
        $conn->receiveData(function ($data){
            $d = json_decode($data)->data;
            assertTrue(count($d)===2);
            assertTrue($d[0]->gender==="male");
        });
        $queue->push(new RequestPacket(json_encode(
            [
                "id" => 1,
                "client_id" => 3,
                "action" => "get-users",
                "token" => $token,
                "parameter" => "male"
            ]
        ), $conn));
        Event::signal("new-request");
    }
    public function test_user_id_is_available_if_request_is_authenticated()
    {
        $ts = new TokenService();
        $token = $ts->generateToken(1);
        $queue = $GLOBALS['api_queue'] = new \Ds\Queue();
        \Amot\Conversate\Facades\Conversate::addAction("get-users", SampleUserService::class, "getUserId",true);
        new RequestConsumer();
        $conn = new FakeConn();
        $conn->receiveData(function ($data){
            $d = json_decode($data)->data;
            assertTrue(intval($d)===1);
        });
        $queue->push(new RequestPacket(json_encode(
            [
                "id" => 1,
                "client_id" => 3,
                "action" => "get-users",
                "token" => $token,
                "parameter" => "male"
            ]
        ), $conn));
        Event::signal("new-request");
    }
    protected function getPackageProviders($app)
    {
        return [
            'Amot\Conversate\ConversateServiceProvider',
        ];
    }
}
