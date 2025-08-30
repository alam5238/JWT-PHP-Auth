<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$username = trim($input['username'] ?? '');
$password = $input['password'] ?? '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'username & password required']);
    exit;
}

$hash = password_hash($password, PASSWORD_BCRYPT);
try {
    $stmt = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)');
    $stmt->execute([$username, $hash]);
    echo json_encode(['success' => true, 'message' => 'Signup successful']);
} catch (PDOException $e) {
    // duplicate username?
    if ($e->getCode() === '23000') {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error']);
    }
}
