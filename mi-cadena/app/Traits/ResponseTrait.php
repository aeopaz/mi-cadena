<?php


namespace App\Traits;

trait ResponseTrait
{

    public function server_response_ok($message, $data, $cod = 200)
    {

        return response()->json([
            "message" => $message,
            "data" => $data
        ], $cod);
    }
    public function server_response_error($message, $data, $cod = 401)
    {

        return response()->json([
            "message" => $message,
            "data" => $data
        ], $cod);
    }
}
