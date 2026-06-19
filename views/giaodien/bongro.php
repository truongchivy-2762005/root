<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Bóng rổ - VAB';
    $trangHienTai = 'sport-basketball';
    include 'dautrang.php';
}

if (!class_exists('ProductModel')) {
    require './models/ProductModel.php';
}
$productModel = new ProductModel();
$products = $productModel->getBySport('Bóng rổ');

// Phân chia sản phẩm theo danh mục nhỏ để hiển thị dạng nhóm
$aoBasketball = array_filter($products, function($p) { return $p['category'] === 'Áo'; });
$quanBasketball = array_filter($products, function($p) { return $p['category'] === 'Quần'; });
$giayBasketball = array_filter($products, function($p) { return $p['category'] === 'Giày'; });
$phuKienBasketball = array_filter($products, function($p) { return $p['category'] === 'Phụ kiện'; });

$categories = [
    ['Áo bóng rổ', $aoBasketball],
    ['Quần bóng rổ', $quanBasketball],
    ['Giày bóng rổ', $giayBasketball],
    ['Phụ kiện bóng rổ', $phuKienBasketball],
];
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1>BÓNG RỔ</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item"><a href="index.php?page=sport-basketball">Thể thao</a></li><li class="breadcrumb-item active">Bóng rổ</li></ol></nav>
</div></section>

<section class="container"><div class="category-banner" style="background:linear-gradient(135deg,#1a1a2e,#8b3a1a);">
    <div class="banner-content">
        <h1>THỜI TRANG BÓNG RỔ</h1>
        <p>Jersey NBA, áo tập, quần short và giày bóng rổ cao cấp. Phong cách sân đấu, thoải mái trên mọi sân.</p>
        <a href="index.php?page=products" class="btn btn-category">Xem tất cả</a>
    </div>
</div></section>

<section class="pb-5"><div class="container">
    <?php foreach($categories as $cat): ?>
    <div class="category-group">
        <h3 class="category-title"><?= $cat[0] ?></h3>
        <div class="row g-3">
            <?php if (!empty($cat[1])): foreach(array_slice($cat[1], 0, 4) as $product): ?>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="product-card">
                    <div class="product-image">
                        <span class="product-badge badge-hot">Bóng rổ</span>
                        <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                        <?php $productId = $product['MaSP']; include __DIR__ . '/partials/product-actions.php'; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?= htmlspecialchars($product['category']) ?></div>
                        <a href="index.php?page=product-detail&id=<?= (int)$product['MaSP'] ?>" class="product-name"><?= htmlspecialchars($product['TenSP']) ?></a>
                        <div class="product-price">
                            <?php if (!empty($product['GiaKhuyenMai']) && $product['GiaKhuyenMai'] > 0): ?>
                            <span class="current-price"><?= number_format($product['GiaKhuyenMai'],0,',',',') ?>đ</span>
                            <span class="old-price"><?= number_format($product['Gia'],0,',',',') ?>đ</span>
                            <?php else: ?>
                            <span class="current-price"><?= number_format($product['Gia'],0,',',',') ?>đ</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <div class="col-12 text-center py-4 text-muted"><p class="mb-0">Chưa có sản phẩm nào thuộc nhóm này.</p></div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div></section>
<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>

