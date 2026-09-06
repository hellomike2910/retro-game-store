<?php
require_once __DIR__ . '/includes/app.php';
$title = 'Liên hệ';
require __DIR__ . '/includes/header.php';
?>
<section class="page container narrow"><h1>Liên hệ với chúng tôi</h1><p>Lời nhắn được lưu trong ứng dụng để phục vụ bài thực hành.</p>
<form id="contact-form" class="panel form-stack" action="actions.php" method="post">
  <?= csrf_field() ?><input type="hidden" name="action" value="contact">
  <div class="form-group"><label for="name-contact">Họ và tên</label><input id="name-contact" name="name" type="text" autocomplete="name" maxlength="100" value="<?= e(old_input('name')) ?>" required></div>
  <div class="form-group"><label for="email-contact">Email</label><input id="email-contact" name="email" type="email" autocomplete="email" maxlength="254" value="<?= e(old_input('email')) ?>" required></div>
  <div class="form-group"><label for="message">Nội dung phản hồi</label><textarea id="message" name="message" maxlength="5000" rows="6" required><?= e(old_input('message')) ?></textarea></div>
  <button type="submit">Lưu lời nhắn</button>
</form></section>
<?php unset($_SESSION['old_input']); require __DIR__ . '/includes/footer.php'; ?>
