<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Format successful API response
     * @param mixed $message
     * @param mixed $data
     * @param mixed $status
     * @param mixed $meta
     * @return \Illuminate\Http\JsonResponse
     */
    public function successfulResponse($message="", $data = null, $status = 200, $meta = []): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], $status);
    }

    /**
     * Format API error response
     * @param mixed $message
     * @param mixed $errors
     * @param mixed $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($message, $errors = [], $status = 400): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ],$status);
    }

}
