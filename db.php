<?php
// No BOM, no whitespace before <?php
$DB_HOST = 'localhost';
$DB_NAME = 'jwtauth';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP default

try {
    $pdo = new PDO("mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'DB connection failed', 'detail' => $e->getMessage()]);
    exit;
}
