<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Chạy bộ - VAB';
    $trangHienTai = 'sport-running';
    include 'dautrang.php';
}

if (!class_exists('ProductModel')) {
    require './models/ProductModel.php';
}
$productModel = new ProductModel();
$products = $productModel->getBySport('Chạy bộ');

// Phân chia sản phẩm theo danh mục nhỏ để hiển thị dạng nhóm
$aoRunning = array_filter($products, function($p) { return $p['category'] === 'Áo'; });
$quanRunning = array_filter($products, function($p) { return $p['category'] === 'Quần'; });
$giayRunning = array_filter($products, function($p) { return $p['category'] === 'Giày'; });
$phuKienRunning = array_filter($products, function($p) { return $p['category'] === 'Phụ kiện'; });

$categories = [
    ['Áo chạy bộ', $aoRunning],
    ['Quần chạy bộ', $quanRunning],
    ['Giày chạy bộ', $giayRunning],
    ['Phụ kiện chạy bộ', $phuKienRunning],
];
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1>CHẠY BỘ</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item"><a href="index.php?page=sport-running">Thể thao</a></li><li class="breadcrumb-item active">Chạy bộ</li></ol></nav>
</div></section>

<section class="container"><div class="category-banner" style="background:linear-gradient(135deg,#0a1628,#1a4a6e);">
    <div class="banner-content">
        <h1>THỜI TRANG CHẠY BỘ</h1>
        <p>Trang phục và giày chạy bộ chính hãng Nike, Adidas, Puma. Nhẹ, thoáng khí, hỗ trợ vận động tối ưu cho mọi cự ly.</p>
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
                        <span class="product-badge badge-new">Chạy bộ</span>
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
