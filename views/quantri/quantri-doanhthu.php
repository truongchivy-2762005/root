<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý doanh thu - VAB Admin';
    $trangHienTai = 'admin-revenue';
    include 'admin-header.php';
}
?>
<!-- Tab Navigation -->
<ul class="nav nav-tabs mb-4" id="revenueTabs" style="border-color:var(--admin-border);">
    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#daily"    style="color:var(--admin-text);background:transparent;border:none;border-bottom:2px solid transparent;"><i class="fas fa-calendar-day me-1"></i>Theo ngày</a></li>
    <li class="nav-item"><a class="nav-link"        data-bs-toggle="tab" href="#monthly"  style="color:var(--admin-text);background:transparent;border:none;border-bottom:2px solid transparent;"><i class="fas fa-calendar-alt me-1"></i>Theo tháng</a></li>
    <li class="nav-item"><a class="nav-link"        data-bs-toggle="tab" href="#yearly"   style="color:var(--admin-text);background:transparent;border:none;border-bottom:2px solid transparent;"><i class="fas fa-calendar me-1"></i>Theo năm</a></li>
    <li class="nav-item"><a class="nav-link"        data-bs-toggle="tab" href="#products" style="color:var(--admin-text);background:transparent;border:none;border-bottom:2px solid transparent;"><i class="fas fa-chart-bar me-1"></i>Sản phẩm</a></li>
</ul>

<div class="tab-content">

<!-- Daily Revenue -->
<div class="tab-pane fade show active" id="daily">
<div class="admin-table-wrapper">
<table class="admin-table">
<thead><tr><th>Ngày</th><th>Số đơn</th><th>Doanh thu</th></tr></thead>
<tbody>
<?php if (empty($dailyRevenue)): ?>
<tr><td colspan="3" class="text-center py-4">Chưa có dữ liệu</td></tr>
<?php else: ?>
<?php foreach($dailyRevenue as $d): ?>
<tr>
<td data-label="Ngày"><strong><?= date('d/m/Y', strtotime($d['day'])) ?></strong></td>
<td data-label="Số đơn" class="text-center"><?= $d['order_count'] ?></td>
<td data-label="Doanh thu" class="fw-bold text-success"><?= number_format($d['revenue'],0,',',',') ?>đ</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<!-- Monthly Revenue -->
<div class="tab-pane fade" id="monthly">
<div class="admin-table-wrapper">
<table class="admin-table">
<thead><tr><th>Tháng</th><th>Số đơn</th><th>Doanh thu</th></tr></thead>
<tbody>
<?php if (empty($monthlyRevenue)): ?>
<tr><td colspan="3" class="text-center py-4">Chưa có dữ liệu</td></tr>
<?php else: ?>
<?php foreach($monthlyRevenue as $m): ?>
<tr>
<td data-label="Tháng"><strong><?= date('m/Y', strtotime($m['month'].'-01')) ?></strong></td>
<td data-label="Số đơn" class="text-center"><?= $m['order_count'] ?></td>
<td data-label="Doanh thu" class="fw-bold text-success"><?= number_format($m['revenue'],0,',',',') ?>đ</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<!-- Yearly Revenue -->
<div class="tab-pane fade" id="yearly">
<div class="admin-table-wrapper">
<table class="admin-table">
<thead><tr><th>Năm</th><th>Số đơn</th><th>Doanh thu</th></tr></thead>
<tbody>
<?php if (empty($yearlyRevenue)): ?>
<tr><td colspan="3" class="text-center py-4">Chưa có dữ liệu</td></tr>
<?php else: ?>
<?php foreach($yearlyRevenue as $y): ?>
<tr>
<td data-label="Năm"><strong><?= $y['year'] ?></strong></td>
<td data-label="Số đơn" class="text-center"><?= $y['order_count'] ?></td>
<td data-label="Doanh thu" class="fw-bold text-success"><?= number_format($y['revenue'],0,',',',') ?>đ</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>

<!-- Products Analysis -->
<div class="tab-pane fade" id="products">
<div class="row g-3">
<div class="col-lg-6">
<div class="admin-table-wrapper">
<h6 class="mb-3" style="color:#10b981;"><i class="fas fa-arrow-up me-1"></i>Sản phẩm bán chạy nhất</h6>
<table class="admin-table">
<thead><tr><th>#</th><th>Sản phẩm</th><th>Đã bán</th><th>Doanh thu</th></tr></thead>
<tbody>
<?php if (empty($bestSellers)): ?>
<tr><td colspan="4" class="text-center py-4">Chưa có dữ liệu</td></tr>
<?php else: ?>
<?php $i=1; foreach($bestSellers as $b): ?>
<tr>
<td data-label="#"><span class="badge bg-success"><?= $i++ ?></span></td>
<td data-label="Sản phẩm"><?= htmlspecialchars($b['TenSP']) ?></td>
<td data-label="Đã bán" class="text-center fw-bold"><?= $b['total_sold'] ?></td>
<td data-label="Doanh thu" class="fw-bold"><?= number_format($b['total_revenue'],0,',',',') ?>đ</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
<div class="col-lg-6">
<div class="admin-table-wrapper">
<h6 class="mb-3" style="color:#ef4444;"><i class="fas fa-arrow-down me-1"></i>Sản phẩm bán ít nhất</h6>
<table class="admin-table">
<thead><tr><th>#</th><th>Sản phẩm</th><th>Đã bán</th><th>Doanh thu</th></tr></thead>
<tbody>
<?php if (empty($worstSellers)): ?>
<tr><td colspan="4" class="text-center py-4">Chưa có dữ liệu</td></tr>
<?php else: ?>
<?php $i=1; foreach($worstSellers as $w): ?>
<tr>
<td data-label="#"><span class="badge bg-danger"><?= $i++ ?></span></td>
<td data-label="Sản phẩm"><?= htmlspecialchars($w['TenSP']) ?></td>
<td data-label="Đã bán" class="text-center fw-bold"><?= $w['total_sold'] ?></td>
<td data-label="Doanh thu" class="fw-bold"><?= number_format($w['total_revenue'],0,',',',') ?>đ</td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</div>

</div>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>