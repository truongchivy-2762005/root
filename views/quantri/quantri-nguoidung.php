<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Quản lý khách hàng - VAB Admin';
    $trangHienTai = 'admin-users';
    include 'admin-header.php';
}
$msg = $_GET['msg'] ?? '';
if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= $msg == 'toggled' ? '✅ Đã cập nhật trạng thái tài khoản!' : '' ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<div class="admin-table-wrapper">
<div class="table-header">
<div class="table-actions">
    <div class="user-search-box">
        <i class="fas fa-search user-search-icon"></i>
        <input type="search" id="userSearchInput" class="form-control" placeholder="Tìm tên, email, SĐT...">
    </div>
</div>
</div>
<table class="admin-table" id="usersTable"><thead><tr><th>Mã KH</th><th>Họ tên</th><th>Email</th><th>SĐT</th><th>Hạng</th><th>Trạng thái</th><th>Ngày tạo</th><th>Hành động</th></tr></thead>
<tbody id="usersTableBody">
<?php if (empty($users)): ?>
<tr><td colspan="8" class="text-center py-4">Chưa có khách hàng nào</td></tr>
<?php else: ?>
<?php 
$tierMap = [
    'silver' => 'Bạc',
    'gold' => 'Vàng',
    'diamond' => 'Kim cương'
];
foreach($users as $u): ?>
<tr data-id="<?= $u['MaKH'] ?>">
<td data-label="Mã KH"><small>KH<?= $u['MaKH'] ?></small></td>
<td data-label="Họ tên"><strong><?= htmlspecialchars($u['HoTenKH']) ?></strong></td>
<td data-label="Email"><small><?= htmlspecialchars($u['email']) ?></small></td>
<td data-label="SĐT"><?= htmlspecialchars($u['SDT'] ?? '') ?></td>
<td data-label="Hạng">
    <span class="tier-badge tier-<?= $u['HangKH'] ?? 'silver' ?>">
        <i class="fas fa-<?= ($u['HangKH']??'silver')=='diamond'?'gem':(($u['HangKH']??'silver')=='gold'?'crown':'medal') ?>"></i>
        <?= $tierMap[$u['HangKH'] ?? 'silver'] ?? ucfirst($u['HangKH'] ?? 'silver') ?>
    </span>
</td>
<td data-label="Trạng thái"><span class="status-badge <?= $u['TrangThai']=='active'?'active':'inactive' ?>"><?= $u['TrangThai']=='active'?'Hoạt động':'Khóa' ?></span></td>
<td data-label="Ngày tạo"><small><?= date('d/m/Y', strtotime($u['NgayTao'])) ?></small></td>
<td data-label="Hành động"><div class="action-btns">
<a href="index.php?page=admin-users&toggle=<?= $u['MaKH'] ?>" class="btn-action <?= $u['TrangThai']=='active'?'btn-delete':'btn-edit' ?>" onclick="return confirm('<?= $u['TrangThai']=='active'?'Khóa':'Mở khóa' ?> tài khoản <?= addslashes($u['HoTenKH']) ?>?')"><i class="fas <?= $u['TrangThai']=='active'?'fa-lock':'fa-unlock' ?>"></i></a>
</div></td></tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody></table>
<div id="noUserResult" class="text-center py-4" style="display:none;">Không tìm thấy khách hàng nào</div>
</div>
<style>
.tier-badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
.tier-diamond { background:linear-gradient(135deg,#e8f4fd,#b3e0ff); color:#0099ff; border:1px solid #66c2ff; }
.tier-gold { background:linear-gradient(135deg,#fff8e1,#ffe082); color:#f57f17; border:1px solid #ffb300; }
.tier-silver { background:linear-gradient(135deg,#f5f5f5,#e0e0e0); color:#616161; border:1px solid #bdbdbd; }
.user-search-box { position:relative; }
.user-search-icon { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--admin-muted); pointer-events:none; font-size:13px; }
.user-search-box .form-control { padding-left:34px; width:260px; border-radius:8px; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('userSearchInput');
    if (!input) return;
    input.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#usersTableBody tr[data-id]');
        let found = 0;
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (!q || text.includes(q)) {
                row.style.display = '';
                found++;
            } else {
                row.style.display = 'none';
            }
        });
        document.getElementById('noUserResult').style.display = found === 0 ? '' : 'none';
    });
});
</script>
<?php if (!isset($nhungTuController)) { include 'admin-footer.php'; } ?>