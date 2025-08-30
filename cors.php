<?php
// Put this FIRST in every API file, above any output.
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // dev: allow all; restrict in prod
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
// End CORS