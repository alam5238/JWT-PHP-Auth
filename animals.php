<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/db.php';
require __DIR__ . '/jwt_utils.php';

$claims = require_user(); // 401s if invalid
$userId = (int)$claims['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

function json_input(): array {
    $raw = file_get_contents('php://input') ?: '';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

switch ($method) {
    case 'GET':
        // List only current user's animals
        $stmt = $pdo->prepare('SELECT id, name, type, created_at FROM animals WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        echo json_encode($stmt->fetchAll());
        break;

    case 'POST':
        $data = json_input();
        $name = trim($data['name'] ?? '');
        $type = trim($data['type'] ?? '');
        if ($name === '' || $type === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'name & type required']);
            break;
        }
        $stmt = $pdo->prepare('INSERT INTO animals (user_id, name, type) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $name, $type]);
        echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId(), 'message' => 'Animal added']);
        break;

    case 'PUT':
        $data = json_input();
        $id   = (int)($data['id'] ?? 0);
        $name = trim($data['name'] ?? '');
        $type = trim($data['type'] ?? '');
        if ($id <= 0 || $name === '' || $type === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'id, name & type required']);
            break;
        }
        $stmt = $pdo->prepare('UPDATE animals SET name = ?, type = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$name, $type, $id, $userId]);
        echo json_encode([
            'success' => $stmt->rowCount() > 0,
            'message' => $stmt->rowCount() > 0 ? 'Animal updated' : 'Not found (or not your record)'
        ]);
        break;

    case 'DELETE':
        // Accept id via query string (?id=123) OR JSON body { "id": 123 }
        $id = isset($_GET['id']) ? (int)$_GET['id'] : (int)(json_input()['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'id required']);
            break;
        }
        $stmt = $pdo->prepare('DELETE FROM animals WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        echo json_encode([
            'success' => $stmt->rowCount() > 0,
            'message' => $stmt->rowCount() > 0 ? 'Animal deleted' : 'Not found (or not your record)'
        ]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
