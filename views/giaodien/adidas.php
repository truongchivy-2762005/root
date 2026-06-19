<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Adidas - VAB Thời Trang Thể Thao';
    $trangHienTai = 'brand-adidas';
    include 'dautrang.php';
}

if (!class_exists('ProductModel')) {
    require './models/ProductModel.php';
}
$productModel = new ProductModel();
$products = $productModel->getByBrand('Adidas');

// Phân chia sản phẩm theo danh mục nhỏ để hiển thị dạng nhóm
$giayAdidas = array_filter($products, function($p) { return $p['category'] === 'Giày'; });
$aoAdidas = array_filter($products, function($p) { return $p['category'] === 'Áo'; });
$quanAdidas = array_filter($products, function($p) { return $p['category'] === 'Quần'; });

$categories = [
    ['Giày Adidas', $giayAdidas],
    ['Áo Adidas', $aoAdidas],
    ['Quần Adidas', $quanAdidas],
];
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1>ADIDAS</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item text-muted">Thương hiệu</li><li class="breadcrumb-item active">Adidas</li></ol></nav>
</div></section>

<!-- Brand Hero -->
<section class="container"><div class="brand-hero" style="background:linear-gradient(135deg,#0a1628,#1a3a5c);">
    <div class="brand-hero-content">
        <img src="https://placehold.co/120x120/f5f0e8/0a1628?text=ADIDAS" alt="Adidas Logo" class="brand-hero-logo">
        <h1>ADIDAS</h1>
        <p class="brand-hero-slogan">"Impossible Is Nothing"</p>
        <p class="brand-hero-desc">Thương hiệu thể thao Đức nổi tiếng với công nghệ Boost, Primeknit và 4D. Adidas mang đến những sản phẩm thời trang thể thao đỉnh cao cho mọi vận động viên.</p>
    </div>
</div></section>

<section class="pb-5"><div class="container">
    <div class="section-title"><h2>SẢN PHẨM ADIDAS</h2><p>Khám phá bộ sưu tập Adidas mới nhất tại VAB</p></div>
    
    <?php foreach($categories as $cat): ?>
    <div class="category-group">
        <h3 class="category-title"><?= $cat[0] ?></h3>
        <div class="row g-3">
            <?php if (!empty($cat[1])): foreach(array_slice($cat[1], 0, 4) as $product): ?>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="product-card">
                    <div class="product-image">
                        <span class="product-badge badge-new">Adidas</span>
                        <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                        <?php $productId = $product['MaSP']; include __DIR__ . '/partials/product-actions.php'; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Adidas</div>
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