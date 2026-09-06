<?php
declare(strict_types=1);

set_exception_handler(static function (Throwable $error): void {
    error_log('Retro Game Store: ' . $error->getMessage());
    http_response_code(503);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!doctype html><html lang="vi"><meta charset="utf-8"><title>Tạm thời chưa thể xử lý</title>';
    echo '<main><h1>Tạm thời chưa thể xử lý</h1><p>Vui lòng thử lại sau. Kiểm tra PHP 8.1+, tiện ích PDO SQLite và quyền ghi thư mục dữ liệu nếu bạn đang chạy website trên máy.</p><a href="index.php">Về trang chủ</a></main></html>';
});

function data_directory(): string
{
    static $resolved = null;
    if ($resolved !== null) {
        return $resolved;
    }
    $projectRoot = dirname(__DIR__);
    $configured = getenv('BTHK2_DATA_DIR');
    $directory = $configured !== false && trim($configured) !== ''
        ? rtrim($configured, DIRECTORY_SEPARATOR)
        : sys_get_temp_dir() . '/bthk2-' . (require __DIR__ . '/storage-id.php');
    if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('Cannot create data directory.');
    }
    $resolved = realpath($directory);
    $documentRoot = realpath($projectRoot);
    if ($resolved === false || $documentRoot === false || $resolved === $documentRoot || str_starts_with($resolved, $documentRoot . DIRECTORY_SEPARATOR)) {
        throw new RuntimeException('BTHK2_DATA_DIR must be outside the website directory.');
    }
    return $resolved;
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    $sessionDirectory = data_directory() . '/sessions';
    if (!is_dir($sessionDirectory) && !@mkdir($sessionDirectory, 0700, true) && !is_dir($sessionDirectory)) {
        throw new RuntimeException('Cannot create session directory.');
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_save_path($sessionDirectory);
    session_name('bthk2_session');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true, 'samesite' => 'Lax',
    ]);
    if (!@session_start()) {
        throw new RuntimeException('Cannot start session. Check session directory permissions.');
    }
}
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('Cache-Control: no-store');

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function db(): PDO
{
    static $connection = null;
    if ($connection instanceof PDO) {
        return $connection;
    }
    $resolved = data_directory();
    $connection = new PDO('sqlite:' . $resolved . '/shop.sqlite', null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $connection->exec('PRAGMA foreign_keys = ON');
    $connection->exec('PRAGMA busy_timeout = 5000');
    $connection->exec(<<<'SQL'
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL COLLATE NOCASE UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL REFERENCES users(id),
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            total INTEGER NOT NULL CHECK(total >= 0),
            name TEXT NOT NULL, phone TEXT NOT NULL, email TEXT NOT NULL,
            country TEXT NOT NULL, address TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'Đã nhận đơn demo',
            checkout_token TEXT NOT NULL UNIQUE
        );
        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
            product_id TEXT NOT NULL, name TEXT NOT NULL,
            price INTEGER NOT NULL CHECK(price >= 0),
            quantity INTEGER NOT NULL CHECK(quantity BETWEEN 1 AND 99)
        );
        CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL, email TEXT NOT NULL, message TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
        CREATE INDEX IF NOT EXISTS orders_user_id ON orders(user_id);
        CREATE INDEX IF NOT EXISTS order_items_order_id ON order_items(order_id);
    SQL);
    @chmod($resolved . '/shop.sqlite', 0600);
    return $connection;
}

function products(): array
{
    static $catalogue = null;
    if ($catalogue === null) {
        $catalogue = require __DIR__ . '/products.php';
    }
    return $catalogue;
}

function product(mixed $id): ?array
{
    return is_scalar($id) ? (products()[(string) $id] ?? null) : null;
}

function money(int $cents): string
{
    return '$' . number_format($cents / 100, 2, '.', ',');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function input(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

function old_input(string $key, string $default = ''): string
{
    return (string) ($_SESSION['old_input'][$key] ?? $default);
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }
    $query = db()->prepare('SELECT id, name, email, created_at FROM users WHERE id = ?');
    $query->execute([(int) $_SESSION['user_id']]);
    $user = $query->fetch();
    if (!$user) {
        unset($_SESSION['user_id']);
        return null;
    }
    return $user;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        flash('Vui lòng đăng nhập để tiếp tục.', 'error');
        redirect('login.php');
    }
    return $user;
}

function cart_items(): array
{
    $items = [];
    foreach (($_SESSION['cart'] ?? []) as $id => $quantity) {
        $item = product($id);
        if (!$item || !is_int($quantity) || $quantity < 1 || $quantity > 99) {
            continue;
        }
        $items[] = ['product' => $item, 'quantity' => $quantity, 'subtotal' => (int) $item['price'] * $quantity];
    }
    return $items;
}

function cart_total(): int
{
    return array_sum(array_column(cart_items(), 'subtotal'));
}

function cart_count(): int
{
    return array_sum(array_column(cart_items(), 'quantity'));
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flashes'][] = ['message' => $message, 'type' => $type];
}

function take_flashes(): array
{
    $messages = $_SESSION['flashes'] ?? [];
    unset($_SESSION['flashes']);
    return $messages;
}

function redirect(string $url): never
{
    header('Location: ' . $url, true, 303);
    exit;
}
