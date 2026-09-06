<?php
require_once __DIR__ . '/includes/app.php';
$id = filter_var($_GET['id'] ?? 1, FILTER_VALIDATE_INT);
$item = $id ? product($id) : null;
$title = $item ? $item['name'] : 'Không tìm thấy sản phẩm';
if (!$item) http_response_code(404);
require __DIR__ . '/includes/header.php';
?>
<section id="single-product" class="page container">
  <p><a href="shop.php">← Quay lại cửa hàng</a></p>
  <?php if (!$item): ?><h1>Không tìm thấy sản phẩm</h1><p>Sản phẩm này không có trong danh mục.</p>
  <?php else: ?>
  <div class="detail-grid">
    <div class="detail-image"><img id="main-product-image" src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>">
    <?php if (count($item['images']) > 1): ?><div class="img-small-group" aria-label="Ảnh sản phẩm">
      <?php foreach ($item['images'] as $i => $photo): ?><a class="small-img-col" href="<?= e($photo) ?>" aria-label="Xem ảnh <?= $i + 1 ?>"><img src="<?= e($photo) ?>" alt="<?= e($item['name']) ?> – ảnh <?= $i + 1 ?>"></a><?php endforeach; ?>
    </div><?php endif; ?></div>
    <div><p class="eyebrow"><?= $item['category'] === 'game' ? 'Trò chơi Nintendo Switch' : 'Máy chơi game' ?></p><h1><?= e($item['name']) ?></h1><p class="price large"><?= money($item['price']) ?></p><p><?= e($item['description']) ?></p>
      <form class="form-stack" action="actions.php" method="post">
        <?= csrf_field() ?><input type="hidden" name="action" value="add_to_cart"><input type="hidden" name="product_id" value="<?= $item['id'] ?>">
        <div class="form-group"><label for="quantity">Số lượng</label><input class="quantity" type="number" id="quantity" name="quantity" value="1" min="1" max="99" required></div>
        <button type="submit">Thêm vào giỏ hàng</button>
      </form><p class="muted">Giá và sản phẩm dùng cho bài thực hành. Đơn đặt thử không thu tiền.</p>
    </div>
  </div>
  <?php endif; ?>
</section>
<script src="js/single_product.js" defer></script>
<?php require __DIR__ . '/includes/footer.php'; ?>
