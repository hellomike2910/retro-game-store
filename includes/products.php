<?php
// Giá USD mẫu được lấy từ hình ảnh có sẵn trong bài tập (đơn vị cent).
$catalog = [
    [1, 'Overcooked! 2', 2499, 'feature/feature1.png', 'game', 'Trò chơi nấu ăn phối hợp dành cho Nintendo Switch.'],
    [2, 'Super Mario Bros. Wonder', 5999, 'feature/feature2.png', 'game', 'Khám phá thế giới của Mario trên Nintendo Switch.'],
    [3, 'Nintendo Switch Sports', 3999, 'feature/feature3.png', 'game', 'Bộ trò chơi thể thao dành cho Nintendo Switch.'],
    [4, 'Among Us', 500, 'feature/feature4.png', 'game', 'Trò chơi suy luận và phối hợp cùng bạn bè.'],
    [5, 'Nintendo Switch + Switch Sports (refurbished)', 29998, 'Nintendo/nintendo1.png', 'console', 'Bộ máy Nintendo Switch tân trang kèm Switch Sports.'],
    [6, 'Nintendo Switch OLED – Super Smash Bros.', 34999, 'Nintendo/nintendo2.png', 'console', 'Bộ Nintendo Switch OLED phiên bản Super Smash Bros.'],
    [7, 'Nintendo Switch – Neon Blue / Neon Red', 29999, 'Nintendo/nintendo3.png', 'console', 'Máy Nintendo Switch với tay cầm Joy-Con xanh và đỏ.'],
    [8, 'Nintendo Switch – Neon Blue / Neon Red (refurbished)', 25999, 'Nintendo/nintendo4.png', 'console', 'Máy Nintendo Switch tân trang với tay cầm Joy-Con xanh và đỏ.'],
];
$result = [];
foreach ($catalog as [$id, $name, $price, $image, $category, $description]) {
    $image = 'imgs/' . $image;
    $result[$id] = compact('id', 'name', 'price', 'image', 'category', 'description');
    $result[$id]['images'] = [$image];
}
$result[2]['images'] = array_merge($result[2]['images'], ['imgs/singleproduct/sp1.png', 'imgs/singleproduct/sp2.png', 'imgs/singleproduct/sp3.png', 'imgs/singleproduct/sp4.png']);
return $result;
