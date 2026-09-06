<?php
require_once __DIR__ . '/includes/app.php';
$title = 'Cửa hàng';
$category = isset($_GET['category']) && is_string($_GET['category']) ? $_GET['category'] : '';
$query = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$items = array_filter(products(), function ($p) use ($category, $query) {
    return (!in_array($category, ['game', 'console'], true) || $p['category'] === $category)
        && ($query === '' || stripos($p['name'], $query) !== false);
});
require __DIR__ . '/includes/header.php';
?>
<section id="shop-page" class="page container">
  <p class="eyebrow">Bộ sưu tập</p><h1>Cửa hàng</h1>
  <form class="shop-filters" action="shop.php" method="get">
    <div><label for="q">Tìm sản phẩm</label><input id="q" name="q" type="search" maxlength="100" value="<?= e($query) ?>" placeholder="Mario, Nintendo…"></div>
    <div><label for="category">Danh mục</label><select id="category" name="category"><option value="">Tất cả</option><option value="game" <?= $category === 'game' ? 'selected' : '' ?>>Trò chơi</option><option value="console" <?= $category === 'console' ? 'selected' : '' ?>>Máy chơi game</option></select></div>
    <button type="submit">Tìm kiếm</button>
  </form>
  <p class="muted"><?= count($items) ?> sản phẩm · Giá minh họa bằng USD</p>
  <?php if (!$items): ?><div class="panel"><p>Không tìm thấy sản phẩm phù hợp.</p><a href="shop.php">Xem toàn bộ sản phẩm</a></div><?php endif; ?>
  <div class="product-grid"><?php foreach ($items as $item) require __DIR__ . '/includes/product_card.php'; ?></div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
