<?php
require_once __DIR__ . '/includes/app.php';
$title = 'Giỏ hàng';
$items = cart_items();
require __DIR__ . '/includes/header.php';
?>
<section id="cart" class="page container"><h1>Giỏ hàng</h1>
<?php if (!$items): ?>
  <div class="panel empty"><h2>Giỏ hàng đang trống</h2><p>Chọn một sản phẩm để bắt đầu.</p><a class="button" href="shop.php">Đến cửa hàng</a></div>
<?php else: ?>
  <div class="table-scroll"><table><thead><tr><th scope="col">Sản phẩm</th><th scope="col">Số lượng</th><th scope="col">Thành tiền</th><th scope="col">Thao tác</th></tr></thead><tbody>
  <?php foreach ($items as $line): $item = $line['product']; ?>
    <tr><td><div class="product-info"><a href="single_product.php?id=<?= $item['id'] ?>"><img src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>"></a><div><a href="single_product.php?id=<?= $item['id'] ?>"><?= e($item['name']) ?></a><p class="muted"><?= money($item['price']) ?> / sản phẩm</p></div></div></td>
    <td><form action="actions.php" method="post" class="quantity-form"><?= csrf_field() ?><input type="hidden" name="action" value="update_cart"><input type="hidden" name="product_id" value="<?= $item['id'] ?>"><input class="quantity" type="number" name="quantity" min="0" max="99" value="<?= $line['quantity'] ?>" required aria-label="Số lượng <?= e($item['name']) ?>"><button type="submit" class="button small secondary">Cập nhật</button></form></td>
    <td class="price"><?= money($line['subtotal']) ?></td><td><form action="actions.php" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="remove_from_cart"><input type="hidden" name="product_id" value="<?= $item['id'] ?>"><button type="submit" class="button small secondary">Xóa</button></form></td></tr>
  <?php endforeach; ?>
  </tbody></table></div>
  <div class="cart-total panel"><p>Tổng cộng <strong class="price" id="cart-total"><?= money(cart_total()) ?></strong></p><p class="muted">Đặt hàng thử · Không thu tiền.</p><a class="button" href="checkout.php">Tiến hành đặt hàng</a></div>
  <a href="shop.php">← Tiếp tục mua sắm</a>
<?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
