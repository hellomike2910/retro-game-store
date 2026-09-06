<?php
declare(strict_types=1);
require __DIR__ . '/includes/app.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Trang này chỉ nhận yêu cầu từ biểu mẫu.');
}
$action = input('action');
$destinations = [
    'register' => 'register.php', 'login' => 'login.php', 'logout' => 'account.php',
    'change_password' => 'account.php', 'add_to_cart' => 'shop.php',
    'update_cart' => 'cart.php', 'remove_from_cart' => 'cart.php',
    'checkout' => 'checkout.php', 'contact' => 'contact.php',
];
$failureUrl = $destinations[$action] ?? 'index.php';
if (!isset($destinations[$action])) {
    flash('Yêu cầu không hợp lệ.', 'error');
    redirect($failureUrl);
}
if (input('csrf_token') === '' || !hash_equals(csrf_token(), input('csrf_token'))) {
    flash('Phiên biểu mẫu đã hết hạn. Vui lòng thử lại.', 'error');
    redirect($failureUrl);
}
// Keep only non-secret values for validation feedback.
$_SESSION['old_input'] = [];
foreach (['name', 'email', 'phone', 'country', 'address', 'message'] as $field) {
    if (isset($_POST[$field]) && is_string($_POST[$field])) {
        $_SESSION['old_input'][$field] = substr(input($field), 0, 20000);
    }
}
function fail_action(string $message): never
{
    global $failureUrl;
    flash($message, 'error');
    redirect($failureUrl);
}
function password_input(string $key): string
{
    return isset($_POST[$key]) && is_string($_POST[$key]) ? $_POST[$key] : '';
}
function text_length(string $value): int
{
    if (preg_match('//u', $value) !== 1) {
        return -1;
    }
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : preg_match_all('/./us', $value);
}
function check_name_email(): void
{
    $nameLength = text_length(input('name'));
    if ($nameLength < 1 || $nameLength > 100) {
        fail_action('Vui lòng nhập họ tên hợp lệ (tối đa 100 ký tự).');
    }
    if (strlen(input('email')) > 254 || !filter_var(input('email'), FILTER_VALIDATE_EMAIL)) {
        fail_action('Vui lòng nhập địa chỉ email hợp lệ.');
    }
}
function check_new_password(): string
{
    $password = password_input('password');
    if (text_length($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")) {
        fail_action('Mật khẩu cần từ 8 ký tự và không quá dài.');
    }
    if ($password !== password_input('password_confirm')) {
        fail_action('Mật khẩu nhập lại chưa khớp.');
    }
    return $password;
}
function start_user_session(int $id): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $id;
    unset($_SESSION['old_input'], $_SESSION['checkout_token'], $_SESSION['last_checkout_token'], $_SESSION['last_order_id']);
}

try {
    switch ($action) {
        case 'register':
            check_name_email();
            $password = check_new_password();
            $query = db()->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
            try {
                $query->execute([input('name'), strtolower(input('email')), password_hash($password, PASSWORD_DEFAULT)]);
            } catch (PDOException $error) {
                if ((string) $error->getCode() === '23000') {
                    fail_action('Email này đã có tài khoản. Vui lòng đăng nhập hoặc dùng email khác.');
                }
                throw $error;
            }
            start_user_session((int) db()->lastInsertId());
            flash('Đăng ký thành công. Chào mừng bạn đến với Retro Game Store!');
            redirect('account.php');

        case 'login':
            $query = db()->prepare('SELECT id, password_hash FROM users WHERE email = ?');
            $query->execute([strtolower(input('email'))]);
            $user = $query->fetch();
            if (!$user || !password_verify(password_input('password'), $user['password_hash'])) {
                fail_action('Email hoặc mật khẩu chưa đúng.');
            }
            start_user_session((int) $user['id']);
            flash('Đăng nhập thành công.');
            redirect('account.php');

        case 'logout':
            unset($_SESSION['user_id'], $_SESSION['old_input'], $_SESSION['checkout_token'], $_SESSION['last_checkout_token'], $_SESSION['last_order_id']);
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            flash('Bạn đã đăng xuất.');
            redirect('login.php');

        case 'change_password':
            $user = require_login();
            $query = db()->prepare('SELECT password_hash FROM users WHERE id = ?');
            $query->execute([$user['id']]);
            if (!password_verify(password_input('current_password'), (string) $query->fetchColumn())) {
                fail_action('Mật khẩu hiện tại chưa đúng.');
            }
            $password = check_new_password();
            $query = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $query->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
            session_regenerate_id(true);
            unset($_SESSION['old_input']);
            flash('Đã đổi mật khẩu thành công.');
            redirect('account.php');

        case 'add_to_cart':
        case 'update_cart':
        case 'remove_from_cart':
            $item = product(input('product_id'));
            if (!$item) {
                fail_action('Không tìm thấy sản phẩm này.');
            }
            $id = (string) $item['id'];
            if ($action === 'remove_from_cart') {
                unset($_SESSION['cart'][$id]);
                flash('Đã xóa sản phẩm khỏi giỏ hàng.');
                redirect('cart.php');
            }
            $quantity = filter_var(input('quantity'), FILTER_VALIDATE_INT);
            if ($quantity === false || $quantity > 99 || $quantity < ($action === 'update_cart' ? 0 : 1)) {
                fail_action('Số lượng phải là số nguyên từ ' . ($action === 'update_cart' ? '0' : '1') . ' đến 99.');
            }
            if ($action === 'add_to_cart') {
                $quantity += (int) ($_SESSION['cart'][$id] ?? 0);
                if ($quantity > 99) {
                    fail_action('Mỗi sản phẩm chỉ được thêm tối đa 99 sản phẩm vào giỏ hàng.');
                }
            }
            if ($quantity === 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id] = $quantity;
            }
            flash($action === 'add_to_cart' ? 'Đã thêm sản phẩm vào giỏ hàng.' : 'Đã cập nhật giỏ hàng.');
            redirect('cart.php');

        case 'checkout':
            $user = require_login();
            $token = input('checkout_token');
            if ($token !== '' && isset($_SESSION['last_checkout_token']) && hash_equals($_SESSION['last_checkout_token'], $token)) {
                flash('Đơn hàng này đã được ghi nhận; không tạo thêm đơn trùng.');
                redirect('account.php#orders');
            }
            if ($token === '' || !isset($_SESSION['checkout_token']) || !hash_equals($_SESSION['checkout_token'], $token)) {
                fail_action('Phiên đặt hàng đã hết hạn. Vui lòng kiểm tra giỏ hàng và thử lại.');
            }
            $items = cart_items();
            if (!$items) {
                flash('Giỏ hàng đang trống. Vui lòng chọn sản phẩm trước khi đặt hàng.', 'error');
                redirect('shop.php');
            }
            check_name_email();
            $phone = input('phone');
            $phoneDigits = preg_replace('/[^0-9]/', '', $phone);
            if (!preg_match('/^[0-9+() -]{7,25}$/D', $phone) || strlen($phoneDigits) < 7 || strlen($phoneDigits) > 15) {
                fail_action('Vui lòng nhập số điện thoại hợp lệ gồm 7–15 chữ số.');
            }
            $countryLength = text_length(input('country'));
            $addressLength = text_length(input('address'));
            if ($countryLength < 1 || $countryLength > 100 || $addressLength < 1 || $addressLength > 500) {
                fail_action('Vui lòng nhập quốc gia và địa chỉ nhận hàng hợp lệ.');
            }
            $connection = db();
            $connection->beginTransaction();
            $query = $connection->prepare('SELECT id FROM orders WHERE checkout_token = ? AND user_id = ?');
            $query->execute([$token, $user['id']]);
            $orderId = $query->fetchColumn();
            if (!$orderId) {
                $query = $connection->prepare('INSERT INTO orders (user_id, total, name, phone, email, country, address, checkout_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
                $query->execute([$user['id'], cart_total(), input('name'), input('phone'), strtolower(input('email')), input('country'), input('address'), $token]);
                $orderId = (int) $connection->lastInsertId();
                $lineQuery = $connection->prepare('INSERT INTO order_items (order_id, product_id, name, price, quantity) VALUES (?, ?, ?, ?, ?)');
                foreach ($items as $line) {
                    $lineQuery->execute([$orderId, (string) $line['product']['id'], $line['product']['name'], (int) $line['product']['price'], $line['quantity']]);
                }
            }
            $connection->commit();
            $_SESSION['last_checkout_token'] = $token;
            $_SESSION['last_order_id'] = (int) $orderId;
            unset($_SESSION['cart'], $_SESSION['checkout_token'], $_SESSION['old_input']);
            flash('Đã lưu đơn hàng demo #' . $orderId . '. Website chưa thu tiền hoặc gửi đơn cho cửa hàng.');
            redirect('account.php#orders');

        case 'contact':
            check_name_email();
            $messageLength = text_length(input('message'));
            if ($messageLength < 1 || $messageLength > 5000) {
                fail_action('Vui lòng nhập nội dung liên hệ (tối đa 5000 ký tự).');
            }
            $query = db()->prepare('INSERT INTO messages (name, email, message) VALUES (?, ?, ?)');
            $query->execute([input('name'), strtolower(input('email')), input('message')]);
            unset($_SESSION['old_input']);
            flash('Đã lưu lời nhắn trong bản demo trên máy này. Chưa gửi email cho cửa hàng.');
            redirect('contact.php');
    }
} catch (Throwable $error) {
    if (isset($connection) && $connection instanceof PDO && $connection->inTransaction()) {
        $connection->rollBack();
    }
    error_log('Retro Game Store action: ' . $error->getMessage());
    fail_action('Hiện chưa thể lưu yêu cầu. Vui lòng thử lại sau hoặc kiểm tra cấu hình lưu trữ.');
}
