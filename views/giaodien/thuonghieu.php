<?php
if (!isset($nhungTuController)) {
    header('Location: index.php');
    exit;
}

$brandSlug = $_GET['brand_slug'] ?? '';
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM THUONGHIEU WHERE DuongDan = ? AND TrangThai = 1");
$stmt->execute([$brandSlug]);
$brandInfo = $stmt->fetch();

if (!$brandInfo) {
    echo '<section class="py-5"><div class="container text-center"><h2>Không tìm thấy thương hiệu hoặc thương hiệu đã bị ẩn</h2><a href="index.php" class="btn btn-secondary rounded-pill mt-3 px-4" style="background:var(--color-secondary);border:none;">Quay lại trang chủ</a></div></section>';
    return;
}

$brandName = $brandInfo['TenThuongHieu'];
$products = $productModel->getByBrand($brandName);

// Phân chia sản phẩm theo danh mục nhỏ để hiển thị dạng nhóm
$giay = array_filter($products, function($p) { return $p['category'] === 'Giày'; });
$ao = array_filter($products, function($p) { return $p['category'] === 'Áo'; });
$quan = array_filter($products, function($p) { return $p['category'] === 'Quần'; });
$phukien = array_filter($products, function($p) { return $p['category'] === 'Phụ kiện'; });

$categories = [
    ['Giày ' . $brandName, $giay],
    ['Áo ' . $brandName, $ao],
    ['Quần ' . $brandName, $quan],
    ['Phụ kiện ' . $brandName, $phukien],
];
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1><?= strtoupper($brandName) ?></h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item text-muted">Thương hiệu</li><li class="breadcrumb-item active"><?= htmlspecialchars($brandName) ?></li></ol></nav>
</div></section>

<!-- Brand Hero -->
<section class="container"><div class="brand-hero" style="background:linear-gradient(135deg,#0a1628,#2d4b3c);">
    <div class="brand-hero-content">
        <img src="https://placehold.co/120x120/f5f0e8/0a1628?text=<?= urlencode(strtoupper($brandName)) ?>" alt="<?= htmlspecialchars($brandName) ?> Logo" class="brand-hero-logo">
        <h1><?= htmlspecialchars($brandName) ?></h1>
        <p class="brand-hero-desc"><?= htmlspecialchars($brandInfo['MoTa'] ?? '') ?></p>
    </div>
</div></section>

<!-- Product Categories -->
<section class="pb-5"><div class="container">
    <div class="section-title"><h2>SẢN PHẨM <?= strtoupper($brandName) ?></h2><p>Khám phá bộ sưu tập <?= htmlspecialchars($brandName) ?> mới nhất tại VAB</p></div>
    
    <?php foreach($categories as $cat): ?>
    <div class="category-group">
        <h3 class="category-title"><?= $cat[0] ?></h3>
        <div class="row g-3">
            <?php if (!empty($cat[1])): foreach(array_slice($cat[1], 0, 4) as $product): ?>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="product-card">
                    <div class="product-image">
                        <span class="product-badge badge-new"><?= htmlspecialchars($brandName) ?></span>
                        <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                        <?php $productId = $product['MaSP']; include __DIR__ . '/partials/product-actions.php'; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?= htmlspecialchars($brandName) ?></div>
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
