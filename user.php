<?php
require __DIR__ . '/cors.php';
require __DIR__ . '/jwt_utils.php';

$claims = require_user();
echo json_encode(['success' => true, 'user_id' => $claims['user_id'], 'username' => $claims['username']]);
