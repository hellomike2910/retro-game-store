<?php
declare(strict_types=1);
require __DIR__ . '/includes/app.php';
if (current_user()) {
    redirect('account.php');
}
$title = 'Đăng ký tài khoản';
require __DIR__ . '/includes/header.php';
?>
<section class="page">
  <div class="container">
    <section class="panel auth-panel">
      <h1>Đăng ký tài khoản</h1>
      <p>Tạo tài khoản để lưu đơn hàng demo của bạn.</p>
      <form action="actions.php" method="post" class="form-stack">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="register">
        <div class="form-group">
          <label for="register-name">Họ và tên</label>
          <input id="register-name" type="text" name="name" autocomplete="name" maxlength="100" value="<?= e(old_input('name')) ?>" required>
        </div>
        <div class="form-group">
          <label for="register-email">Email</label>
          <input id="register-email" type="email" name="email" autocomplete="username" maxlength="254" value="<?= e(old_input('email')) ?>" required>
        </div>
        <div class="form-group">
          <label for="register-password">Mật khẩu</label>
          <input id="register-password" type="password" name="password" autocomplete="new-password" minlength="8" maxlength="72" aria-describedby="password-help" required>
          <small id="password-help">Ít nhất 8 ký tự.</small>
        </div>
        <div class="form-group">
          <label for="register-password-confirm">Nhập lại mật khẩu</label>
          <input id="register-password-confirm" type="password" name="password_confirm" autocomplete="new-password" minlength="8" maxlength="72" required>
        </div>
        <button class="button" type="submit">Đăng ký</button>
      </form>
      <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </section>
  </div>
</section>
<?php unset($_SESSION['old_input']); require __DIR__ . '/includes/footer.php'; ?>
