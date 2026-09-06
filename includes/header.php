<?php
require_once __DIR__ . '/app.php';
$page = basename($_SERVER['SCRIPT_NAME']);
$user = current_user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Trang chủ') ?> | Retro Game Store</title>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/app.js" defer></script>
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="logo" href="index.php" aria-label="Retro Game Store – Trang chủ"><img src="imgs/logo.png" alt="Retro Game Store"></a>
    <button class="menu-toggle" type="button" aria-controls="main-nav" aria-expanded="false">Menu</button>
    <nav id="main-nav" aria-label="Điều hướng chính">
      <?php foreach (['index.php' => 'Trang chủ', 'shop.php' => 'Cửa hàng', 'contact.php' => 'Liên hệ'] as $url => $label): ?>
        <a href="<?= e($url) ?>" <?= $page === $url ? 'aria-current="page"' : '' ?>><?= e($label) ?></a>
      <?php endforeach; ?>
      <a href="cart.php" <?= $page === 'cart.php' ? 'aria-current="page"' : '' ?>>Giỏ hàng <span class="badge"><?= cart_count() ?></span></a>
      <a href="<?= $user ? 'account.php' : 'login.php' ?>" <?= in_array($page, ['account.php', 'login.php', 'register.php'], true) ? 'aria-current="page"' : '' ?>><?= $user ? 'Tài khoản' : 'Đăng nhập' ?></a>
    </nav>
  </div>
</header>
<main>
<?php foreach (take_flashes() as $notice): ?>
  <div class="container"><div class="alert <?= $notice['type'] === 'error' ? 'alert-error' : 'alert-success' ?>" role="<?= $notice['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($notice['message']) ?></div></div>
<?php endforeach; ?>
