<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function ok($data = null, string $message = '', int $status = 200) 
    {
        return response()->json(
            [
                "code" => $status,
                "message" => $message, 
                "data" => $data
            ], $status);
    }

    protected function created(string $message = '', int $status = 201) 
    {
        return response()->json(
            [
                "code" => $status,
                "message" => $message,
                "data" => []
            ], $status);
    }

    protected function error($message, int $status = 500, array $errors = []) 
    {
        return response()->json([
            'code'      => $status,
            'message'   => $message,
            'errors'    => $errors
        ], $status);
    }
}
