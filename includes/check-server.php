<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$port = filter_var($argv[1] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
if (!$port) { fwrite(STDERR, "PORT phải là số từ 1 đến 65535.\n"); exit(1); }
$socket = @fsockopen('127.0.0.1', $port, $errno, $error, 1);
if (!$socket) exit(0);
fclose($socket);
$context = stream_context_create(['http' => ['timeout' => 2, 'ignore_errors' => true]]);
$body = @file_get_contents("http://127.0.0.1:$port/health.php", false, $context);
$status = $body === false ? null : json_decode($body, true);
if (is_array($status) && ($status['app'] ?? '') === 'retro-game-store' && ($status['status'] ?? '') === 'ok'
    && ($status['instance'] ?? '') === (require __DIR__ . '/storage-id.php')) {
    echo "RETRO_GAME_STORE_READY http://127.0.0.1:$port\n";
    echo "Website đã chạy. Mở địa chỉ trên trong trình duyệt.\n";
    exit(10);
}
fwrite(STDERR, "Cổng $port đang được một máy chủ khác sử dụng.\nDừng máy chủ cũ bằng Ctrl+C, hoặc chạy: PORT=8081 sh start.command\nNếu dùng F5, hãy giải phóng cổng 8080 rồi thử lại.\n");
exit(1);
