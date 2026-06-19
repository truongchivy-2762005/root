<?php
$productId = (int)($productId ?? ($product['MaSP'] ?? 0));
$hasStock = ProductModel::hasStock($productId);
?>
<div class="product-actions">
    <a href="index.php?page=product-detail&id=<?= $productId ?>" class="btn-action btn-view-detail" title="Xem chi tiết">
        <i class="fas fa-eye"></i>
    </a>
    <?php if ($hasStock): ?>
        <a href="index.php?page=add-to-cart&id=<?= $productId ?>" class="btn-action btn-add-cart" title="Thêm vào giỏ hàng">
            <i class="fas fa-shopping-cart"></i>
        </a>
    <?php else: ?>
        <a href="javascript:void(0)" class="btn-action btn-add-cart disabled" title="Hết hàng" style="opacity:0.5; cursor:not-allowed;">
            <i class="fas fa-shopping-cart"></i>
        </a>
    <?php endif; ?>
</div>
