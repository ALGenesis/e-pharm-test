<?php

function sendJsonResponse($data = null, $statusCode = 200, $message = '') {
    
    echo json_encode([
        'success' => $statusCode >= 200 && $statusCode < 300,
        'message' => $message,
        'data' => $data,
        'status' => $statusCode
    ]);
    exit;
}