<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Bảng điều khiển - VAB Admin';
    $trangHienTai = 'admin-dashboard';
    include 'admin-header.php';
}
?>
<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(37,99,235,0.1);color:#2563eb;"><i class="fas fa-dollar-sign"></i></div>
            <div class="stat-info"><span class="stat-label">Doanh thu</span><span class="stat-value"><?= number_format($totalRevenue,0,',',',') ?>đ</span></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="fas fa-shopping-bag"></i></div>
            <div class="stat-info"><span class="stat-label">Đơn hàng</span><span class="stat-value"><?= $totalOrders ?></span></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,158,11,0.1);color:#f59e0b;"><i class="fas fa-users"></i></div>
            <div class="stat-info"><span class="stat-label">Người dùng</span><span class="stat-value"><?= $totalUsers ?></span></div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(239,68,68,0.1);color:#ef4444;"><i class="fas fa-box"></i></div>
            <div class="stat-info"><span class="stat-label">Sản phẩm</span><span class="stat-value"><?= $totalProducts ?></span></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="admin-table-wrapper">
            <h5 class="mb-3">Đơn hàng gần đây</h5>
            <table class="admin-table">
                <thead><tr><th>Mã ĐH</th><th>Khách hàng</th><th>Tổng</th><th>Trạng thái</th><th>Ngày</th></tr></thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                    <tr><td colspan="5" class="text-center py-3">Chưa có đơn hàng nào</td></tr>
                    <?php else: ?>
                    <?php 
                    $statusMap = [
                        'pending' => 'Chờ xác nhận',
                        'confirmed' => 'Đã thanh toán',
                        'shipping' => 'Đang giao',
                        'delivered' => 'Đã giao',
                        'completed' => 'Giao thành công',
                        'cancelled' => 'Đã hủy'
                    ];
                    foreach($recentOrders as $o): ?>
                    <tr>
                        <td data-label="Mã ĐH"><small>#ORD<?= str_pad($o['MaDonHang'],3,'0',STR_PAD_LEFT) ?></small></td>
                        <td data-label="Khách hàng"><?= htmlspecialchars($o['customer_name'] ?? $o['HoTenKH']) ?></td>
                        <td data-label="Tổng"><?= number_format($o['TongTien'],0,',',',') ?>đ</td>
                        <td data-label="Trạng thái"><span class="status-badge <?= $o['TrangThaiDH'] ?>"><?= $statusMap[$o['TrangThaiDH']] ?? $o['TrangThaiDH'] ?></span></td>
                        <td data-label="Ngày"><?= date('d/m/Y', strtotime($o['NgayTao'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-table-wrapper">
            <h5 class="mb-3">Tồn kho thấp</h5>
            <?php if (empty($lowStock)): ?>
            <p class="text-muted">Không có sản phẩm nào tồn kho thấp</p>
            <?php else: ?>
            <?php foreach($lowStock as $s): ?>
            <div class="stock-item"><span><?= htmlspecialchars($s['TenSP']) ?></span><span class="text-danger fw-bold">Còn <?= $s['tong_kho'] ?></span></div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>