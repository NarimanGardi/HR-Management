<?php

namespace App\Traits;

Trait HttpResponses {
    
        public function successResponse($data, $code = 200) {
            return response()->json([
                'data' => $data,
                'status' => 'success',
                'code' => $code
            ], $code);
        }
    
        public function errorResponse($message, $code = 400) {
            return response()->json([
                'message' => $message,
                'status' => 'error',
                'code' => $code
            ], $code);
        }
}