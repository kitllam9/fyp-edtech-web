<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function success(
        $data = null,
        $message = '',
        $statusCode = 200
    ) {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
        ], $statusCode);
    }

    protected function failed(
        $data = null,
        $message = '',
        $errors = null,
        $statusCode = 400,
    ) {
        return response()->json([
            'success' => false,
            'data' => $data,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }
}
