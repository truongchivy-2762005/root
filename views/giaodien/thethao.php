<?php
if (!isset($nhungTuController)) {
    header('Location: index.php');
    exit;
}

$sportSlug = $_GET['sport_slug'] ?? '';
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM THETHAO WHERE DuongDan = ? AND TrangThai = 1");
$stmt->execute([$sportSlug]);
$sportInfo = $stmt->fetch();

if (!$sportInfo) {
    echo '<section class="py-5"><div class="container text-center"><h2>Không tìm thấy bộ môn thể thao hoặc bộ môn này đã bị ẩn</h2><a href="index.php" class="btn btn-secondary rounded-pill mt-3 px-4" style="background:var(--color-secondary);border:none;">Quay lại trang chủ</a></div></section>';
    return;
}

$sportName = $sportInfo['TenSport'];
$products = $productModel->getBySport($sportName);

// Phân chia sản phẩm theo danh mục nhỏ để hiển thị dạng nhóm
$ao = array_filter($products, function($p) { return $p['category'] === 'Áo'; });
$quan = array_filter($products, function($p) { return $p['category'] === 'Quần'; });
$giay = array_filter($products, function($p) { return $p['category'] === 'Giày'; });
$phukien = array_filter($products, function($p) { return $p['category'] === 'Phụ kiện'; });

$categories = [
    ['Áo ' . strtolower($sportName), $ao],
    ['Quần ' . strtolower($sportName), $quan],
    ['Giày ' . strtolower($sportName), $giay],
    ['Phụ kiện ' . strtolower($sportName), $phukien],
];
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1><?= strtoupper($sportName) ?></h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item"><a href="#">Thể thao</a></li><li class="breadcrumb-item active"><?= htmlspecialchars($sportName) ?></li></ol></nav>
</div></section>

<section class="container"><div class="category-banner" style="background:linear-gradient(135deg,#0a1628,#1a4a6e);">
    <div class="banner-content">
        <h1>THỜI TRANG <?= strtoupper($sportName) ?></h1>
        <p>Trang phục và phụ kiện chuyên dụng cho bộ môn <?= htmlspecialchars($sportName) ?>. Hỗ trợ vận động tối ưu.</p>
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
                        <span class="product-badge badge-new"><?= htmlspecialchars($sportName) ?></span>
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
