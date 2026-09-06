<?php
require_once __DIR__ . '/includes/app.php';
$title = 'Trang chủ';
require __DIR__ . '/includes/header.php';
?>
<section id="home" class="hero">
  <div class="container"><div class="hero-copy"><p class="eyebrow">New products</p><h1>Best price<br>on the market</h1><p>Khám phá thế giới Nintendo tại Retro Game Store.</p><a class="button" href="shop.php">Mua sắm ngay</a></div></div>
</section>
<section id="brand" class="container brands" aria-label="Thương hiệu">
  <?php for ($i = 1; $i <= 4; $i++): ?><img src="imgs/brand/brand<?= $i ?>.png" alt="Thương hiệu game <?= $i ?>"><?php endfor; ?>
</section>
<section id="new" class="container promo-grid">
  <?php foreach (['Trò chơi', 'Máy cầm tay', 'Khám phá thêm'] as $i => $label): ?>
    <a class="promo" href="shop.php<?= $i === 0 ? '?category=game' : ($i === 1 ? '?category=console' : '') ?>"><img src="imgs/New/new<?= $i + 1 ?>.png" alt=""><span><?= e($label) ?> →</span></a>
  <?php endforeach; ?>
</section>
<section id="feature" class="container section-space">
  <div class="section-heading"><div><p class="eyebrow">Nintendo Switch</p><h2>Special games</h2><p>Những trò chơi nổi bật trong bộ sưu tập.</p></div><a href="shop.php?category=game">Xem tất cả →</a></div>
  <div class="product-grid"><?php foreach (array_filter(products(), fn($p) => $p['category'] === 'game') as $item) require __DIR__ . '/includes/product_card.php'; ?></div>
</section>
<section id="sale" class="sale-banner"><div class="container"><div class="hero-copy"><p class="eyebrow">Retro Game Store</p><h2>Mang cuộc vui<br>đi khắp nơi.</h2><a class="button" href="shop.php?category=console">Xem máy chơi game</a></div></div></section>
<section id="nintendo" class="container section-space">
  <div class="section-heading"><div><p class="eyebrow">Handheld consoles</p><h2>Máy chơi game cầm tay</h2><p>Lựa chọn máy cho trải nghiệm tiếp theo của bạn.</p></div><a href="shop.php?category=console">Xem tất cả →</a></div>
  <div class="product-grid"><?php foreach (array_filter(products(), fn($p) => $p['category'] === 'console') as $item) require __DIR__ . '/includes/product_card.php'; ?></div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
