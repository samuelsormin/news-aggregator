<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    /**
     * Send success response
     * 
     * @param array $data
     * @param string $message
     * @param integer $code
     * 
     * @return JsonResponse
     */
    public function sendResponse($message, $data = [], $code = 200): JsonResponse
    {
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data
        ];

        return response()->json($response, $code);
    }

    /**
     * Send error response
     * 
     * @param string $message
     * @param integer $code
     * 
     * @return JsonResponse
     */
    public function sendError($message, $errors = [], $code = 400): JsonResponse
    {
        $response = [
            'status' => false,
            'message' =>  $message,
            'errors' => $errors,
        ];

        return response()->json($response, $code);
    }
}
