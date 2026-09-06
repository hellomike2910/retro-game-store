<?php
require __DIR__ . '/includes/app.php';
db()->query('SELECT 1');
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['app' => 'retro-game-store', 'instance' => require __DIR__ . '/includes/storage-id.php', 'status' => 'ok']);
