<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Nike - VAB Thời Trang Thể Thao';
    $trangHienTai = 'brand-nike';
    include 'dautrang.php';
}

if (!class_exists('ProductModel')) {
    require './models/ProductModel.php';
}
$productModel = new ProductModel();
$products = $productModel->getByBrand('Nike');

// Phân chia sản phẩm theo danh mục nhỏ để hiển thị dạng nhóm
$giayNike = array_filter($products, function($p) { return $p['category'] === 'Giày'; });
$aoNike = array_filter($products, function($p) { return $p['category'] === 'Áo'; });
$quanNike = array_filter($products, function($p) { return $p['category'] === 'Quần'; });

$categories = [
    ['Giày Nike', $giayNike],
    ['Áo Nike', $aoNike],
    ['Quần Nike', $quanNike],
];
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1>NIKE</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item text-muted">Thương hiệu</li><li class="breadcrumb-item active">Nike</li></ol></nav>
</div></section>

<!-- Brand Hero -->
<section class="container"><div class="brand-hero" style="background:linear-gradient(135deg,#0a1628,#2d4b3c);">
    <div class="brand-hero-content">
        <img src="https://placehold.co/120x120/f5f0e8/0a1628?text=NIKE" alt="Nike Logo" class="brand-hero-logo">
        <h1>NIKE</h1>
        <p class="brand-hero-slogan">"Just Do It"</p>
        <p class="brand-hero-desc">Thương hiệu thể thao số 1 thế giới. Nike cung cấp giày dép, trang phục và phụ kiện thể thao chất lượng cao với công nghệ đột phá như Dri-FIT, Air Max, Flyknit và Zoom Air.</p>
    </div>
</div></section>

<!-- Product Categories -->
<section class="pb-5"><div class="container">
    <div class="section-title"><h2>SẢN PHẨM NIKE</h2><p>Khám phá bộ sưu tập Nike mới nhất tại VAB</p></div>
    
    <?php foreach($categories as $cat): ?>
    <div class="category-group">
        <h3 class="category-title"><?= $cat[0] ?></h3>
        <div class="row g-3">
            <?php if (!empty($cat[1])): foreach(array_slice($cat[1], 0, 4) as $product): ?>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="product-card">
                    <div class="product-image">
                        <span class="product-badge badge-new">Nike</span>
                        <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                        <?php $productId = $product['MaSP']; include __DIR__ . '/partials/product-actions.php'; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Nike</div>
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