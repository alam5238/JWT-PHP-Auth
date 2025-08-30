<?php
require __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

const JWT_SECRET = 'CHANGE_THIS_TO_A_LONG_RANDOM_SECRET';

function create_jwt(array $claims): string {
    // default 1 hour expiry
    $now = time();
    $payload = array_merge([
        'iat' => $now,
        'exp' => $now + 3600, // 1 hour
        'iss' => 'jwt-project'
    ], $claims);

    return JWT::encode($payload, JWT_SECRET, 'HS256');
}

function get_bearer_token(): ?string {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    // handle lowercase key too
    $auth = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) return $m[1];
    return null;
}

function require_user(): array {
    $token = get_bearer_token();
    if (!$token) {
        http_response_code(401);
        echo json_encode(['error' => 'Missing Authorization header']);
        exit;
    }
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET, 'HS256'));
        // return as array
        return (array)$decoded;
    } catch (Throwable $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid/Expired token', 'detail' => $e->getMessage()]);
        exit;
    }
}
