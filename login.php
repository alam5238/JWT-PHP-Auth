<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/db.php';
require __DIR__ . '/jwt_utils.php';

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

$stmt = $pdo->prepare('SELECT id, username, password FROM users WHERE username = ? LIMIT 1');
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

$token = create_jwt([
    'user_id'  => (int)$user['id'],
    'username' => $user['username'],
]);

echo json_encode([
    'success'  => true,
    'token'    => $token,
    'username' => $user['username'],
]);
