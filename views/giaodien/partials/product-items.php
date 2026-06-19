<?php

/**
 * Partial view for rendering product items (used for both initial load and AJAX load more)
 * Expects: $products array, $hasMore bool (optional for AJAX)
 */
$isAjax = !empty($hasMore) || isset($hasMore);
?>
<?php if (!empty($products)): foreach ($products as $product): ?>
        <div class="col-lg-4 col-md-6 col-6 product-item" data-id="<?= (int)$product['MaSP'] ?>">
            <div class="product-card">
                <div class="product-image" style="position: relative;">
                    <img src="<?= ProductModel::getImageUrl($product) ?>" alt="<?= htmlspecialchars($product['TenSP']) ?>">
                    <?php if (!ProductModel::hasStock((int)$product['MaSP'])): ?>
                        <span class="badge bg-danger position-absolute top-0 start-0 m-2 px-2.5 py-1.5 fw-bold" style="font-size: 11px; z-index: 2; border-radius: 4px; background-color: #dc3545 !important; color: #fff;">HẾT HÀNG</span>
                    <?php endif; ?>
                    <?php $productId = $product['MaSP'];
                    include __DIR__ . '/product-actions.php'; ?>
                </div>
                <div class="product-info">
                    <div class="product-category"><?= htmlspecialchars($product['category'] ?? 'Sản phẩm') ?></div>
                    <a href="index.php?page=product-detail&id=<?= (int)$product['MaSP'] ?>" class="product-name"><?= htmlspecialchars($product['TenSP']) ?></a>
                    <div class="product-price">
                        <?php if (!empty($product['GiaKhuyenMai']) && $product['GiaKhuyenMai'] > 0): ?>
                            <span class="current-price"><?= number_format($product['GiaKhuyenMai'], 0, ',', ',') ?>đ</span>
                            <span class="old-price"><?= number_format($product['Gia'], 0, ',', ',') ?>đ</span>
                        <?php else: ?>
                            <span class="current-price"><?= number_format($product['Gia'], 0, ',', ',') ?>đ</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;
else: ?>
    <div class="col-12 text-center py-5 load-more-empty">
        <p class="text-muted">Không còn sản phẩm nào.</p>
    </div>
<?php endif; ?>
<?php if ($isAjax && isset($loaded) && isset($total)): ?>
    <div class="col-12 load-more-data" data-loaded="<?= $loaded ?>" data-total="<?= $total ?>" data-hasmore="<?= $hasMore ? '1' : '0' ?>"></div>
<?php endif; ?>