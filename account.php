<?php
declare(strict_types=1);
require __DIR__ . '/includes/app.php';
$user = require_login();
$query = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
$query->execute([$user['id']]);
$orders = $query->fetchAll();
$lineQuery = db()->prepare('SELECT * FROM order_items WHERE order_id = ? ORDER BY id');
$title = 'Tài khoản của bạn';
require __DIR__ . '/includes/header.php';
?>
<section class="page">
  <div class="container">
    <h1>Tài khoản của bạn</h1>
    <div class="account-grid">
      <section class="panel">
        <h2>Thông tin tài khoản</h2>
        <p><strong>Họ và tên:</strong> <?= e($user['name']) ?></p>
        <p><strong>Email:</strong> <?= e($user['email']) ?></p>
        <p><a href="#orders">Xem đơn hàng của bạn</a></p>
        <form action="actions.php" method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="logout">
          <button class="button button-secondary" type="submit">Đăng xuất</button>
        </form>
      </section>
      <section class="panel">
        <h2>Đổi mật khẩu</h2>
        <form action="actions.php" method="post" class="form-stack">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="change_password">
          <div class="form-group">
            <label for="current-password">Mật khẩu hiện tại</label>
            <input id="current-password" type="password" name="current_password" autocomplete="current-password" required>
          </div>
          <div class="form-group">
            <label for="new-password">Mật khẩu mới</label>
            <input id="new-password" type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72" required>
          </div>
          <div class="form-group">
            <label for="confirm-password">Nhập lại mật khẩu mới</label>
            <input id="confirm-password" type="password" name="password_confirm" autocomplete="new-password" minlength="8" maxlength="72" required>
          </div>
          <button class="button" type="submit">Đổi mật khẩu</button>
        </form>
      </section>
    </div>
    <section id="orders" class="orders-section">
      <h2>Đơn hàng của bạn</h2>
      <p>Đây là các đơn hàng demo đã lưu trên máy này; chưa thanh toán và chưa gửi đến cửa hàng.</p>
      <?php if (!$orders): ?>
        <div class="panel"><p>Bạn chưa có đơn hàng nào.</p><a class="button" href="shop.php">Xem sản phẩm</a></div>
      <?php endif; ?>
      <?php foreach ($orders as $order): $lineQuery->execute([$order['id']]); ?>
        <article class="panel order-card">
          <h3>Đơn hàng #<?= e($order['id']) ?></h3>
          <p><?= e($order['created_at']) ?> UTC · <?= e($order['status']) ?></p>
          <ul class="order-items">
            <?php foreach ($lineQuery->fetchAll() as $line): ?>
              <li><?= e($line['name']) ?> × <?= e($line['quantity']) ?> — <?= e(money((int) $line['price'] * (int) $line['quantity'])) ?></li>
            <?php endforeach; ?>
          </ul>
          <p><strong>Tổng cộng: <?= e(money((int) $order['total'])) ?></strong></p>
          <p><strong>Người nhận:</strong> <?= e($order['name']) ?> · <?= e($order['phone']) ?></p>
          <p><strong>Địa chỉ:</strong> <?= e($order['address']) ?>, <?= e($order['country']) ?></p>
        </article>
      <?php endforeach; ?>
    </section>
  </div>
</section>
<?php unset($_SESSION['old_input']); require __DIR__ . '/includes/footer.php'; ?>
