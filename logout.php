<?php
require __DIR__ . '/cors.php';
// Stateless JWT: server cannot "destroy" the token unless you implement a blacklist.
// For this demo, logout is client-side: just delete the token in browser/app.
echo json_encode(['success' => true, 'message' => 'Logged out (client should delete token)']);
