<?php
declare(strict_types=1);
require __DIR__ . '/includes/app.php';
if (current_user()) {
    redirect('account.php');
}
$title = 'Đăng nhập';
require __DIR__ . '/includes/header.php';
?>
<section class="page">
  <div class="container">
    <section class="panel auth-panel">
      <h1>Đăng nhập</h1>
      <p>Đăng nhập để đặt hàng và xem những đơn hàng đã lưu.</p>
      <form action="actions.php" method="post" class="form-stack">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="login">
        <div class="form-group">
          <label for="login-email">Email</label>
          <input id="login-email" type="email" name="email" autocomplete="username" maxlength="254" value="<?= e(old_input('email')) ?>" required>
        </div>
        <div class="form-group">
          <label for="login-password">Mật khẩu</label>
          <input id="login-password" type="password" name="password" autocomplete="current-password" required>
        </div>
        <button class="button" type="submit">Đăng nhập</button>
      </form>
      <p>Chưa có tài khoản? <a href="register.php">Đăng ký</a></p>
    </section>
  </div>
</section>
<?php unset($_SESSION['old_input']); require __DIR__ . '/includes/footer.php'; ?>
