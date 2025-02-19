<?php

namespace App\Helpers;

trait ApiResponse
{
    public function success($message, $data = null)
    {
        $response = [
            'success' => (bool)true,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response, 200);
    }

    public function error($message, $data = null)
    {
        $response = [
            'success' => (bool)false,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response, 400);
    }
}
