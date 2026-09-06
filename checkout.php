<?php
require_once __DIR__ . '/includes/app.php';
require_login();
if (!cart_items()) { flash('Giỏ hàng đang trống. Hãy chọn sản phẩm trước khi đặt hàng.', 'error'); redirect('cart.php'); }
$user = current_user();
$_SESSION['checkout_token'] = $_SESSION['checkout_token'] ?? bin2hex(random_bytes(32));
$title = 'Đặt hàng';
require __DIR__ . '/includes/header.php';
?>
<section class="page container"><h1>Đặt hàng</h1><p>Điền thông tin nhận hàng để lưu đơn đặt thử.</p>
<div class="checkout-grid">
<form id="checkout-form" class="panel form-stack" action="actions.php" method="post">
  <?= csrf_field() ?><input type="hidden" name="action" value="checkout"><input type="hidden" name="checkout_token" value="<?= e($_SESSION['checkout_token']) ?>">
  <div class="form-group"><label for="name-check">Họ và tên</label><input id="name-check" name="name" type="text" autocomplete="name" maxlength="100" value="<?= e(old_input('name') ?: $user['name']) ?>" required></div>
  <div class="form-group"><label for="call-check">Số điện thoại</label><input id="call-check" name="phone" type="tel" autocomplete="tel" minlength="7" maxlength="25" value="<?= e(old_input('phone')) ?>" required></div>
  <div class="form-group"><label for="email-check">Email</label><input id="email-check" name="email" type="email" autocomplete="email" maxlength="254" value="<?= e(old_input('email') ?: $user['email']) ?>" required></div>
  <div class="form-group"><label for="country">Quốc gia</label><input id="country" name="country" type="text" autocomplete="country-name" maxlength="100" value="<?= e(old_input('country') ?: 'Việt Nam') ?>" required></div>
  <div class="form-group"><label for="address">Địa chỉ nhận hàng</label><textarea id="address" name="address" autocomplete="street-address" maxlength="500" required><?= e(old_input('address')) ?></textarea></div>
  <p class="muted">Đơn được lưu trong ứng dụng. Không có bước thanh toán trực tuyến hoặc thu tiền.</p>
  <button type="submit">Xác nhận đặt hàng thử</button>
</form>
<aside class="panel order-summary"><h2>Đơn hàng của bạn</h2>
<?php foreach (cart_items() as $line): ?><div class="summary-line"><span><?= e($line['product']['name']) ?> × <?= $line['quantity'] ?></span><strong><?= money($line['subtotal']) ?></strong></div><?php endforeach; ?>
<div class="summary-line total"><span>Tổng cộng</span><strong><?= money(cart_total()) ?></strong></div><a href="cart.php">Chỉnh sửa giỏ hàng</a>
</aside></div>
</section>
<?php unset($_SESSION['old_input']); require __DIR__ . '/includes/footer.php'; ?>
