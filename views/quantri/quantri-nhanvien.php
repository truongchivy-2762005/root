<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý nhân viên - VAB Admin';
    $trangHienTai = 'admin-employees';
    include 'admin-header.php';
}
$msg = $_GET['msg'] ?? '';
if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= $msg == 'added' ? '✅ Thêm nhân viên thành công!' : ($msg == 'toggled' ? '✅ Đã cập nhật trạng thái!' : '') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<div class="admin-table-wrapper">
<div class="table-header">
<div class="table-actions">
<button class="btn btn-secondary" style="background:var(--admin-secondary);border:none;border-radius:8px;" data-bs-toggle="modal" data-bs-target="#addEmployeeModal"><i class="fas fa-plus me-1"></i>Thêm nhân viên</button>
</div></div>
<table class="admin-table"><thead><tr><th>Mã NV</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Vai trò</th><th>Trạng thái</th><th>Ngày tạo</th><th>Hành động</th></tr></thead>
<tbody>
<?php if (empty($employees)): ?>
<tr><td colspan="8" class="text-center py-4">Chưa có nhân viên nào</td></tr>
<?php else: ?>
<?php 
$roleMap = [
    'admin' => 'Quản trị viên',
    'manager' => 'Quản lý',
    'staff' => 'Nhân viên'
];
foreach($employees as $e): ?>
<tr>
<td data-label="Mã NV"><small>NV<?= $e['id'] ?></small></td>
<td data-label="Họ tên"><strong><?= htmlspecialchars($e['HoTenNV']) ?></strong></td>
<td data-label="Email"><small><?= htmlspecialchars($e['email']) ?></small></td>
<td data-label="SĐT"><?= htmlspecialchars($e['SDT'] ?? '') ?></td>
<td data-label="Vai trò"><span class="status-badge <?= $e['VaiTro']=='admin'?'completed':($e['VaiTro']=='manager'?'active':'') ?>"><?= $roleMap[$e['VaiTro']] ?? ucfirst($e['VaiTro']) ?></span></td>
<td data-label="Trạng thái"><span class="status-badge <?= $e['TrangThai']=='active'?'active':'inactive' ?>"><?= $e['TrangThai']=='active'?'Hoạt động':'Khóa' ?></span></td>
<td data-label="Ngày tạo"><small><?= date('d/m/Y', strtotime($e['NgayLapTK'])) ?></small></td>
<td data-label="Hành động"><div class="action-btns">
<a href="index.php?page=admin-employees&toggle=<?= $e['id'] ?>" class="btn-action <?= $e['TrangThai']=='active'?'btn-delete':'btn-edit' ?>" onclick="return confirm('<?= $e['TrangThai']=='active'?'Khóa':'Mở khóa' ?> nhân viên <?= addslashes($e['HoTenNV']) ?>?')"><i class="fas <?= $e['TrangThai']=='active'?'fa-lock':'fa-unlock' ?>"></i></a>
</div></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table>
</div>

<!-- Modal Thêm nhân viên -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1">
<div class="modal-dialog"><div class="modal-content" style="background:var(--admin-card);color:var(--admin-text);border:1px solid var(--admin-border);">
<div class="modal-header border-0"><h5 class="modal-title"><i class="fas fa-user-tie me-2"></i>Thêm nhân viên mới</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
<form method="POST">
<div class="modal-body">
<div class="form-group mb-3"><label>Họ tên</label><input type="text" name="name" class="form-control" required></div>
<div class="form-group mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
<div class="form-group mb-3"><label>Số điện thoại</label><input type="text" name="phone" class="form-control"></div>
<div class="form-group mb-3"><label>Mật khẩu</label><input type="password" name="password" class="form-control" placeholder="Mặc định: 123456"></div>
<div class="form-group mb-3"><label>Vai trò</label>
<select name="role" class="form-select">
    <option value="staff">Nhân viên</option>
    <option value="manager">Quản lý</option>
    <option value="admin">Admin</option>
</select></div>
</div>
<div class="modal-footer border-0"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background:var(--admin-muted);border:none;">Hủy</button>
<button type="submit" name="add_employee" class="btn btn-primary" style="background:var(--admin-secondary);border:none;">Thêm nhân viên</button></div>
</form>
</div></div></div>

<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>