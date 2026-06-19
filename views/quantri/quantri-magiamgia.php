<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý mã giảm giá - VAB Admin';
    $trangHienTai = 'admin-vouchers';
    include 'admin-header.php';
}
$msg = $_GET['msg'] ?? '';
if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= $msg == 'added' ? '✅ Thêm mã giảm giá thành công!' : ($msg == 'deleted' ? '🗑️ Đã xóa mã giảm giá!' : '') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<div class="admin-table-wrapper">
<div class="table-header">
<div class="table-actions">
<button class="btn btn-secondary" style="background:var(--admin-secondary);border:none;border-radius:8px;" data-bs-toggle="modal" data-bs-target="#addVoucherModal"><i class="fas fa-plus me-1"></i>Thêm mã giảm giá</button>
</div></div>
<table class="admin-table"><thead><tr><th>ID</th><th>Mã</th><th>Tên</th><th>Loại</th><th>Giá trị</th><th>Đơn tối thiểu</th><th>Số lượt</th><th>Đã dùng</th><th>Thời hạn</th><th>Trạng thái</th><th>Hành động</th></tr></thead>
<tbody>
<?php if (empty($vouchers)): ?>
<tr><td colspan="11" class="text-center py-4">Chưa có mã giảm giá nào</td></tr>
<?php else: ?>
<?php foreach($vouchers as $v): ?>
<?php 
$now = date('Y-m-d');
$expired = $v['NgayKT'] < $now;
$status = $v['TrangThai'];
if ($expired && $status == 'active') $status = 'expired';
?>
<tr data-id="<?= $v['id'] ?>">
<td data-label="ID"><small><?= $v['id'] ?></small></td>
<td data-label="Mã"><strong class="text-uppercase"><?= htmlspecialchars($v['MaGiamGia']) ?></strong></td>
<td data-label="Tên"><?= htmlspecialchars($v['Ten']) ?></td>
<td data-label="Loại"><?= $v['Loai']=='percent'?'%':'VNĐ' ?></td>
<td data-label="Giá trị"><?= $v['Loai']=='percent' ? $v['GiaTri'].'%' : number_format($v['GiaTri'],0,',',',') . 'đ' ?></td>
<td data-label="Đơn tối thiểu"><?= number_format($v['DonHangToiThieu'],0,',',',') ?>đ</td>
<td data-label="Số lượt"><?= $v['SoLuot'] ?></td>
<td data-label="Đã dùng"><?= $v['DaDung'] ?></td>
<td data-label="Thời hạn"><small><?= date('d/m/Y', strtotime($v['NgayBD'])) ?> - <?= date('d/m/Y', strtotime($v['NgayKT'])) ?></small></td>
<td data-label="Trạng thái"><span class="status-badge <?= $status ?>"><?= $status=='active'?'Hoạt động':($status=='expired'?'Hết hạn':'Ẩn') ?></span></td>
<td data-label="Hành động"><div class="action-btns">
<a href="index.php?page=admin-vouchers&delete=<?= $v['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Xóa mã giảm giá <?= addslashes($v['MaGiamGia']) ?>?')" title="Xóa"><i class="fas fa-trash"></i></a>
</div></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table>
</div>

<!-- Modal Thêm mã giảm giá -->
<div class="modal fade" id="addVoucherModal" tabindex="-1">
<div class="modal-dialog"><div class="modal-content" style="background:var(--admin-card);color:var(--admin-text);border:1px solid var(--admin-border);">
<div class="modal-header border-0"><h5 class="modal-title"><i class="fas fa-ticket-alt me-2"></i>Thêm mã giảm giá mới</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
<form method="POST">
<div class="modal-body">
<div class="row">
<div class="col-md-6"><div class="form-group mb-3"><label>Mã giảm giá</label><input type="text" name="code" class="form-control" placeholder="VD: SALE50" required></div></div>
<div class="col-md-6"><div class="form-group mb-3"><label>Tên mã giảm giá</label><input type="text" name="name" class="form-control" required></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group mb-3"><label>Loại</label><select name="type" class="form-select"><option value="percent">%</option><option value="fixed">VNĐ</option></select></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label>Giá trị</label><input type="number" name="value" class="form-control" required></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label>Đơn tối thiểu</label><input type="number" name="min_order" class="form-control" value="0"></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group mb-3"><label>Số lượt tối đa</label><input type="number" name="max_use" class="form-control" value="100"></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label>Ngày bắt đầu</label><input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>"></div></div>
<div class="col-md-4"><div class="form-group mb-3"><label>Ngày kết thúc</label><input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d', strtotime('+30 days')) ?>"></div></div>
</div>
</div>
<div class="modal-footer border-0">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background:var(--admin-muted);border:none;">Hủy</button>
<button type="submit" name="add_voucher" class="btn btn-primary" style="background:var(--admin-secondary);border:none;">Thêm mã giảm giá</button>
</div>
</form>
</div></div></div>

<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>