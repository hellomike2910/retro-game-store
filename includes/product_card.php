<?php // $item is supplied by the catalogue loop. ?>
<article class="product">
  <a class="product-visual" href="single_product.php?id=<?= $item['id'] ?>"><img loading="lazy" src="<?= e($item['image']) ?>" alt="<?= e($item['name']) ?>"></a>
  <div class="product-body">
    <p class="eyebrow"><?= $item['category'] === 'game' ? 'Trò chơi' : 'Máy chơi game' ?></p>
    <h3><a href="single_product.php?id=<?= $item['id'] ?>"><?= e($item['name']) ?></a></h3>
    <p class="price"><?= money($item['price']) ?></p>
    <a class="button" href="single_product.php?id=<?= $item['id'] ?>">Xem sản phẩm</a>
  </div>
</article>
