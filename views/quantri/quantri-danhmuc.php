<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý danh mục - VAB Admin';
    $trangHienTai = 'admin-categories';
    include 'admin-header.php';
}

$msg = $_GET['msg'] ?? $msg ?? '';
$tab = $_GET['tab'] ?? 'category';
if (!in_array($tab, ['category', 'brand', 'sport'])) {
    $tab = 'category';
}
?>

<?php if ($msg): ?>
<div class="alert alert-<?= ($msg == 'deleted' || $msg == 'error_has_products') ? 'danger' : 'success' ?> alert-dismissible fade show">
    <?php
    if ($msg == 'added') echo '✅ Thêm mới thành công!';
    elseif ($msg == 'updated') echo '✅ Cập nhật trạng thái thành công!';
    elseif ($msg == 'deleted') echo '🗑️ Đã xóa thành công!';
    elseif ($msg == 'error_has_products') echo '⚠️ Bản ghi này đang chứa sản phẩm liên kết, không thể xóa!';
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Tabs Navigation -->
<?php
$activeStyle = "background: var(--admin-secondary); color: #fff; border: 1px solid var(--admin-secondary);";
$inactiveStyle = "background: #fff; color: var(--admin-primary); border: 1px solid #dee2e6;";
?>
<div class="row g-3 mb-4">
    <div class="col-12">
        <ul class="nav nav-pills" style="gap: 10px;">
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=admin-categories&tab=category" style="border-radius: 8px; font-weight: bold; <?= $tab === 'category' ? $activeStyle : $inactiveStyle ?>">📁 Danh mục sản phẩm</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=admin-categories&tab=brand" style="border-radius: 8px; font-weight: bold; <?= $tab === 'brand' ? $activeStyle : $inactiveStyle ?>">🏷️ Danh mục thương hiệu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php?page=admin-categories&tab=sport" style="border-radius: 8px; font-weight: bold; <?= $tab === 'sport' ? $activeStyle : $inactiveStyle ?>">⚽ Danh mục thể thao</a>
            </li>
        </ul>
    </div>
</div>

<div class="row g-3">
    <?php if ($tab === 'category'): ?>
    <!-- TAB 1: DANH MỤC SẢN PHẨM -->
    <div class="col-lg-8">
        <div class="admin-table-wrapper">
            <h5 class="mb-3">Danh sách danh mục sản phẩm</h5>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Mã danh mục</th>
                        <th>Tên danh mục</th>
                        <th style="width: 25%;">Trạng thái</th>
                        <th style="width: 25%; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                        <tr><td colspan="4" class="text-center py-4">Chưa có danh mục nào</td></tr>
                    <?php else: ?>
                        <?php foreach($categories as $cat): ?>
                            <tr>
                                <td data-label="Mã danh mục"><small>#<?= $cat['MaDanhMuc'] ?></small></td>
                                <td data-label="Tên danh mục"><strong><?= htmlspecialchars($cat['TenDM']) ?></strong></td>
                                <td data-label="Trạng thái">
                                    <a href="index.php?page=admin-categories&tab=category&toggle_status=<?= $cat['MaDanhMuc'] ?>" 
                                       class="status-badge <?= $cat['TrangThai'] ? 'active' : 'inactive' ?>" 
                                       style="text-decoration: none;" 
                                       title="Nhấp để thay đổi">
                                        <?= $cat['TrangThai'] ? 'Hoạt động' : 'Đang ẩn' ?>
                                    </a>
                                </td>
                                <td data-label="Hành động" class="text-center">
                                    <div class="action-btns justify-content-center">
                                        <a href="index.php?page=admin-category-delete&tab=category&id=<?= $cat['MaDanhMuc'] ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Xóa danh mục <?= addslashes($cat['TenDM']) ?>?')" 
                                           title="Xóa"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-table-wrapper p-4">
            <h5 class="mb-4">Thêm danh mục mới</h5>
            <form method="POST" action="index.php?page=admin-categories&tab=category">
                <input type="hidden" name="add_category" value="1">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên danh mục *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Áo thun, Giày bóng rổ" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Trạng thái hiển thị *</label>
                    <select name="status" class="form-select">
                        <option value="1">Đang hoạt động (Hiển thị)</option>
                        <option value="0">Đang ẩn</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" style="background: var(--admin-secondary); border: none;">
                    <i class="fas fa-plus me-1"></i> Tạo danh mục mới
                </button>
            </form>
        </div>
    </div>

    <?php elseif ($tab === 'brand'): ?>
    <!-- TAB 2: DANH MỤC THƯƠNG HIỆU -->
    <div class="col-lg-8">
        <div class="admin-table-wrapper">
            <h5 class="mb-3">Danh sách thương hiệu</h5>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Mã TH</th>
                        <th>Tên thương hiệu</th>
                        <th>Mô tả</th>
                        <th style="width: 25%;">Trạng thái</th>
                        <th style="width: 25%; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($brands)): ?>
                        <tr><td colspan="5" class="text-center py-4">Chưa có thương hiệu nào</td></tr>
                    <?php else: ?>
                        <?php foreach($brands as $b): ?>
                            <tr>
                                <td data-label="Mã TH"><small>#<?= $b['MaThuongHieu'] ?></small></td>
                                <td data-label="Tên thương hiệu"><strong><?= htmlspecialchars($b['TenThuongHieu']) ?></strong></td>
                                <td data-label="Mô tả"><small class="text-muted"><?= htmlspecialchars($b['MoTa'] ?? '') ?></small></td>
                                <td data-label="Trạng thái">
                                    <a href="index.php?page=admin-categories&tab=brand&toggle_status=<?= $b['MaThuongHieu'] ?>" 
                                       class="status-badge <?= ($b['TrangThai'] ?? 1) ? 'active' : 'inactive' ?>" 
                                       style="text-decoration: none;" 
                                       title="Nhấp để thay đổi">
                                        <?= ($b['TrangThai'] ?? 1) ? 'Hoạt động' : 'Đang ẩn' ?>
                                    </a>
                                </td>
                                <td data-label="Hành động" class="text-center">
                                    <div class="action-btns justify-content-center">
                                        <a href="index.php?page=admin-category-delete&tab=brand&id=<?= $b['MaThuongHieu'] ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Xóa thương hiệu <?= addslashes($b['TenThuongHieu']) ?>?')" 
                                           title="Xóa"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-table-wrapper p-4">
            <h5 class="mb-4">Thêm thương hiệu mới</h5>
            <form method="POST" action="index.php?page=admin-categories&tab=brand">
                <input type="hidden" name="add_brand" value="1">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên thương hiệu *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Nike, Adidas" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Mô tả ngắn</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Nhập mô tả thương hiệu..."></textarea>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Trạng thái hiển thị *</label>
                    <select name="status" class="form-select">
                        <option value="1">Đang hoạt động (Hiển thị)</option>
                        <option value="0">Đang ẩn</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" style="background: var(--admin-secondary); border: none;">
                    <i class="fas fa-plus me-1"></i> Tạo thương hiệu mới
                </button>
            </form>
        </div>
    </div>

    <?php elseif ($tab === 'sport'): ?>
    <!-- TAB 3: DANH MỤC THỂ THAO -->
    <div class="col-lg-8">
        <div class="admin-table-wrapper">
            <h5 class="mb-3">Danh sách bộ môn thể thao</h5>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Mã thể thao</th>
                        <th>Tên bộ môn</th>
                        <th style="width: 25%;">Trạng thái</th>
                        <th style="width: 25%; text-align: center;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sports)): ?>
                        <tr><td colspan="4" class="text-center py-4">Chưa có bộ môn nào</td></tr>
                    <?php else: ?>
                        <?php foreach($sports as $sp): ?>
                            <tr>
                                <td data-label="Mã thể thao"><small>#<?= $sp['id'] ?></small></td>
                                <td data-label="Tên bộ môn"><strong><?= htmlspecialchars($sp['TenSport']) ?></strong></td>
                                <td data-label="Trạng thái">
                                    <a href="index.php?page=admin-categories&tab=sport&toggle_status=<?= $sp['id'] ?>" 
                                       class="status-badge <?= $sp['TrangThai'] ? 'active' : 'inactive' ?>" 
                                       style="text-decoration: none;" 
                                       title="Nhấp để thay đổi">
                                        <?= $sp['TrangThai'] ? 'Hoạt động' : 'Đang ẩn' ?>
                                    </a>
                                </td>
                                <td data-label="Hành động" class="text-center">
                                    <div class="action-btns justify-content-center">
                                        <a href="index.php?page=admin-category-delete&tab=sport&id=<?= $sp['id'] ?>" 
                                           class="btn-action btn-delete" 
                                           onclick="return confirm('Xóa bộ môn <?= addslashes($sp['TenSport']) ?>?')" 
                                           title="Xóa"><i class="fas fa-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-table-wrapper p-4">
            <h5 class="mb-4">Thêm bộ môn mới</h5>
            <form method="POST" action="index.php?page=admin-categories&tab=sport">
                <input type="hidden" name="add_sport" value="1">
                <div class="mb-3">
                    <label class="form-label fw-bold">Tên bộ môn thể thao *</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Cầu lông, Bóng bàn" required>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-bold">Trạng thái hiển thị *</label>
                    <select name="status" class="form-select">
                        <option value="1">Đang hoạt động (Hiển thị)</option>
                        <option value="0">Đang ẩn</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100 rounded-pill py-2.5 fw-bold" style="background: var(--admin-secondary); border: none;">
                    <i class="fas fa-plus me-1"></i> Tạo bộ môn mới
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>
