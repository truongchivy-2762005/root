<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Sale - VAB';
    $trangHienTai = 'sale';
    include 'dautrang.php';
}
?>
<!-- BREADCRUMB -->
<section class="breadcrumb-section"><div class="container"><h1>KHUYẾN MÃI</h1>
    <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li><li class="breadcrumb-item active">Sale</li></ol></nav>
</div></section>

<section class="container"><div class="sale-banner">
    <h2>BIG SALE - GIẢM ĐẾN 50%</h2>
    <p>Chương trình khuyến mãi lớn nhất năm. Nhanh tay sở hữu ngay!</p>
    <div class="sale-countdown">
        <span class="countdown-item">07</span> :
        <span class="countdown-item">12</span> :
        <span class="countdown-item">45</span> :
        <span class="countdown-item">30</span>
        <p style="margin-top:10px;font-size:14px;opacity:0.8;">Ngày : Giờ : Phút : Giây</p>
    </div>
</div></section>

<section class="pb-5"><div class="container">
    <div class="section-title"><h2>SẢN PHẨM ĐANG GIẢM GIÁ</h2></div>
    <div class="row">
        <?php
        $saleItems = [
            ['Áo Thể Thao Nam Nike Dri-FIT',990000,790000,'20%','sale1',1],
            ['Quần Short Thể Thao Nam Adidas',750000,550000,'27%','sale2',2],
            ['Giày Chạy Bộ Puma Ultra Run',3200000,2450000,'23%','sale3',3],
            ['Áo Khoác Gió Thể Thao Unisex',1690000,1290000,'24%','sale4',4],
            ['Áo Ba Lỗ Gym Nike Pro',890000,650000,'27%','sale5',5],
            ['Balo Thể Thao Adidas',1250000,890000,'29%','sale6',9],
            ['Giày Tennis NikeCourt Air Zoom',4200000,3290000,'22%','sale7',7],
            ['Quần Jogger Thể Thao Nam',990000,690000,'30%','sale8',10],
        ];
        foreach($saleItems as $item):
            $productId = $item[5];
        ?>
        <div class="col-lg-3 col-md-6 col-6">
            <div class="product-card">
                <div class="product-image">
                    <span class="product-badge badge-sale">-<?= $item[3] ?></span>
                    <img src="https://placehold.co/400x500/1a1a2e/f25c19?text=Sale+<?= $item[4] ?>" alt="<?= $item[0] ?>">
                    <?php include __DIR__ . '/partials/product-actions.php'; ?>
                </div>
                <div class="product-info">
                    <div class="product-category">Sale</div>
                    <a href="index.php?page=product-detail&id=<?= $productId ?>" class="product-name"><?= $item[0] ?></a>
                    <div class="product-price">
                        <span class="current-price"><?= number_format($item[2],0,',',',') ?>đ</span>
                        <span class="old-price"><?= number_format($item[1],0,',',',') ?>đ</span>
                        <span class="discount">-<?= $item[3] ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div></section>
<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>