<?php

namespace App\Traits;

trait ApiResponse
{
    protected function successResponse($data, $message = 'Success', $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data'    => $data
        ];

        return response()->json($response, $code);
    }

    protected function errorResponse($errors, $message = 'Error', $code = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors
        ], $code);
    }
}
