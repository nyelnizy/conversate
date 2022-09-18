<?php


namespace Amot\Conversate\Tests\Unit;


use Amot\Conversate\Request;

class TestUserService
{

    public function getUsers(Request $request)
    {
        $res = [["name" => "Daniel", "gender" => $request->parameter], ["name" => "Prince", "gender" => $request->parameter]];
        return $request->complete($res, 200);
    }
    public function getUserId(Request $request)
    {
        return $request->complete($request->user_id, 200);
    }


}