<?php

// Manual API server untuk testing Flutter
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, Accept');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Test endpoint
if ($uri === '/api/test' && $method === 'GET') {
    echo json_encode([
        'success' => true,
        'message' => 'Manual API server is working!',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// Login endpoint
if ($uri === '/api/login' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['email']) && isset($input['password'])) {
        if ($input['email'] === 'admin@test.com' && $input['password'] === 'password') {
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => [
                        'id' => 1,
                        'name' => 'Test Admin',
                        'email' => 'admin@test.com',
                        'employee' => [
                            'id' => 1,
                            'empno' => 'EMP001',
                            'fullname' => 'Test Admin Employee'
                        ]
                    ],
                    'token' => 'mock-token-' . time()
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
        }
    } else {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Email and password required'
        ]);
    }
    exit;
}

// 404 for other routes
http_response_code(404);
echo json_encode([
    'success' => false,
    'message' => 'Route not found'
]);