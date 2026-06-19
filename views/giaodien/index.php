<?php
// Tự động include header nếu mở trực tiếp (không qua controller)
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'VAB - Thời Trang Thể Thao';
    $trangHienTai = 'home';
    include 'dautrang.php';
}
?>
<!-- ===== HERO BANNER CAROUSEL ===== -->
<section class="hero-carousel">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <picture>
                    <source media="(max-width: 575.98px)" srcset="assets/images/banners/banner1-mobile.png">
                    <source media="(max-width: 991.98px)" srcset="assets/images/banners/banner1-tablet.jpg">
                    <img src="assets/images/banners/banner1-desktop.jpg" alt="Thương hiệu thể thao Nike Adidas Puma">
                </picture>
                <div class="carousel-caption d-none d-md-block">
                    <p class="banner-subtitle">Thương hiệu chính hãng</p>
                    <h2 class="banner-title">NIKE - ADIDAS - PUMA</h2>
                    <p class="banner-desc">Sản phẩm thể thao chính hãng từ các thương hiệu </p>
                    <a href="index.php?page=products" class="btn btn-banner btn-banner-primary">Khám phá ngay</a>
                </div>
            </div>
            <div class="carousel-item">
                <picture>
                    <source media="(max-width: 575.98px)" srcset="assets/images/banners/banner2-mobile.png">
                    <source media="(max-width: 991.98px)" srcset="assets/images/banners/banner2-tablet.jpg">
                    <img src="assets/images/banners/banner2-desktop.jpg" alt="Thể thao nam">
                </picture>
                <div class="carousel-caption d-none d-md-block">
                    <p class="banner-subtitle">Thể thao nam</p>
                    <h2 class="banner-title">THỜI TRANG NAM GIỚI</h2>
                    <p class="banner-desc">Trang phục thể thao nam phong cách, chất lượng cao cho mọi hoạt động</p>
                    <a href="index.php?page=men" class="btn btn-banner btn-banner-primary">Xem bộ sưu tập</a>
                </div>
            </div>
            <div class="carousel-item">
                <picture>
                    <source media="(max-width: 575.98px)" srcset="assets/images/banners/banner3-mobile.png">
                    <source media="(max-width: 991.98px)" srcset="assets/images/banners/banner3-tablet.jpg">
                    <img src="assets/images/banners/banner3-desktop.jpg" alt="Thể thao nữ">
                </picture>
                <div class="carousel-caption d-none d-md-block">
                    <p class="banner-subtitle">Thể thao nữ</p>
                    <h2 class="banner-title">PHONG CÁCH NỮ TỰ TIN</h2>
                    <p class="banner-desc">Bộ sưu tập thời trang thể thao nữ năng động và thời thượng</p>
                    <a href="index.php?page=women" class="btn btn-banner btn-banner-primary">Xem bộ sưu tập</a>
                </div>
            </div>
            <div class="carousel-item">
                <picture>
                    <source media="(max-width: 575.98px)" srcset="assets/images/banners/banner4-mobile.png">
                    <source media="(max-width: 991.98px)" srcset="assets/images/banners/banner4-tablet.jpg">
                    <img src="assets/images/banners/banner4-desktop.jpg" alt="Bóng đá">
                </picture>
                <div class="carousel-caption d-none d-md-block">
                    <p class="banner-subtitle">Bóng đá</p>
                    <h2 class="banner-title">ĐAM MÊ BÓNG ĐÁ</h2>
                    <p class="banner-desc">Trang phục và giày bóng đá chính hãng từ các câu lạc bộ </p>
                    <a href="index.php?page=men" class="btn btn-banner btn-banner-primary">Khám phá ngay</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<!-- ===== SẢN PHẨM ===== -->
<section class="py-5">
    <div class="container">
        <div class="section-title">
            <a href="index.php?page=products" class="section-title-link">
                <h2>SẢN PHẨM</h2>
            </a>
            <p>Khám phá bộ sưu tập thời trang thể thao tại VAB</p>
            <a href="index.php?page=products" class="btn-view-all">Xem tất cả sản phẩm</a>
        </div>
        <div class="product-slider-wrap">
            <button type="button" class="product-slider-arrow product-slider-prev" aria-label="Lùi">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="product-slider-viewport">
                <div class="product-slider-track">
                    <?php if (isset($sanPhamMoi) && !empty($sanPhamMoi)): foreach($sanPhamMoi as $product): ?>
                    <div class="product-slider-item">
                        <div class="product-card">
                            <div class="product-image">
                                <span class="product-badge badge-new">Mới</span>
                                <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                                <?php $productId = $product['MaSP']; include __DIR__ . '/partials/product-actions.php'; ?>
                            </div>
                            <div class="product-info">
                                <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Sản phẩm') ?></div>
                                <a href="index.php?page=product-detail&id=<?= (int)$product['MaSP'] ?>" class="product-name"><?= htmlspecialchars($product['TenSP']) ?></a>
                                <div class="product-price">
                                    <span class="current-price"><?= number_format($product['Gia'],0,',',',') ?>đ</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="col-12 text-center py-5" style="width: 100%;"><p class="text-muted">Chưa có sản phẩm nào.</p></div>
                    <?php endif; ?>
                </div>
            </div>
            <button type="button" class="product-slider-arrow product-slider-next" aria-label="Tiến">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</section>

<!-- ===== THỂ THAO ===== -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title">
            <h2>THỂ THAO</h2>
            <p>Khám phá trang phục theo từng bộ môn thể thao</p>
        </div>
        <div class="filter-buttons">
            <a href="index.php?page=products" class="btn-filter active">Tất cả</a>
            <?php
            $home_pdo = getConnection();
            $homeSports = $home_pdo->query("SELECT * FROM THETHAO WHERE TrangThai = 1 ORDER BY id ASC")->fetchAll();
            foreach ($homeSports as $hs):
            ?>
            <a href="index.php?page=<?= htmlspecialchars($hs['DuongDan']) ?>" class="btn-filter"><?= htmlspecialchars($hs['TenSport']) ?></a>
            <?php endforeach; ?>
        </div>
        <div class="row g-3">
            <?php
            if (!class_exists('ProductModel')) {
                require './models/ProductModel.php';
            }
            $productModel = new ProductModel();
            $sportProducts = $productModel->getNewProducts(4);
            foreach($sportProducts as $product):
                $productId = $product['MaSP'];
            ?>
            <div class="col-lg-3 col-md-6 col-6">
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                        <?php include __DIR__ . '/partials/product-actions.php'; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Sản phẩm') ?></div>
                        <a href="index.php?page=product-detail&id=<?= $productId ?>" class="product-name"><?= htmlspecialchars($product['TenSP']) ?></a>
                        <div class="product-price">
                            <?php if (!empty($product['GiaKhuyenMai']) && $product['GiaKhuyenMai'] > 0): ?>
                            <span class="current-price"><?= number_format($product['GiaKhuyenMai'],0,',',',') ?>đ</span>
                            <?php else: ?>
                            <span class="current-price"><?= number_format($product['Gia'],0,',',',') ?>đ</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== THƯƠNG HIỆU ===== -->
<section class="py-5">
    <div class="container">
        <div class="section-title">
            <h2>THƯƠNG HIỆU</h2>
            <p>Các thương hiệu thể thao</p>
        </div>
        <div class="row g-3 g-md-4">
            <?php
            $homeBrands = $home_pdo->query("SELECT * FROM THUONGHIEU WHERE TrangThai = 1 ORDER BY TenThuongHieu ASC")->fetchAll();
            foreach($homeBrands as $b):
                $brandNameUpper = strtoupper($b['TenThuongHieu']);
            ?>
            <div class="col-4 col-md-4">
                <div class="brand-card">
                    <img src="https://placehold.co/200x200/f5f0e8/0a1628?text=<?= $brandNameUpper ?>" alt="<?= htmlspecialchars($b['TenThuongHieu']) ?>" class="brand-logo">
                    <h3 class="brand-name"><?= htmlspecialchars($b['TenThuongHieu']) ?></h3>
                    <p class="brand-slogan">"<?= htmlspecialchars($b['MoTa'] ?? '') ?>"</p>
                    <a href="index.php?page=<?= htmlspecialchars($b['DuongDan']) ?>" class="btn btn-brand"><span class="d-none d-sm-inline">Xem </span><?= htmlspecialchars($b['TenThuongHieu']) ?></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>