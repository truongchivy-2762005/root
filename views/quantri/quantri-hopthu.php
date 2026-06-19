<?php
if (!isset($nhungTuController)) {
    header('Location: index.php?page=admin-mail');
    exit;
}
?>

<div class="container-fluid py-4">
    <!-- TIÊU ĐỀ & NÚT SOẠN MAIL -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-dark fw-bold">Quản lý Email & Thông báo</h3>
        <a href="index.php?page=admin-mail-create" class="btn btn-warning text-white rounded-pill px-4" style="background-color: #f37021; border-color: #f37021;">
            <i class="fas fa-paper-plane me-2"></i>Gửi thư mới
        </a>
    </div>

    <!-- CÁC THÔNG BÁO KẾT QUẢ -->
    <?php if (isset($_SESSION['mail_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 10px;">
            <i class="fas fa-check-circle me-2"></i>
            <?= $_SESSION['mail_success']; unset($_SESSION['mail_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['mail_smtp_errors'])): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0" role="alert" style="border-radius: 10px;">
            <h5 class="alert-heading fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Danh sách Email thật gửi lỗi (vẫn lưu Database thông báo):</h5>
            <ul class="mb-0 ps-3">
                <?php foreach ($_SESSION['mail_smtp_errors'] as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['mail_smtp_errors']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- DANH SÁCH THƯ ĐÃ GỬI -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0 text-dark fw-bold"><i class="fas fa-history me-2 text-muted"></i>Lịch sử gửi thư</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted" style="font-size: 13px; text-transform: uppercase;">
                    <tr>
                        <th class="ps-4">Mã KH</th>
                        <th>Khách nhận</th>
                        <th>Email nhận</th>
                        <th>Tiêu đề</th>
                        <th>Trạng thái web</th>
                        <th>Ngày gửi</th>
                        <th class="pe-4 text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                    <?php if (!empty($mails)): foreach ($mails as $m): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">KH<?= $m['MaKH'] ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($m['HoTenKH'] ?? 'Không rõ') ?></td>
                            <td><span class="text-muted"><?= htmlspecialchars($m['email'] ?? 'Không rõ') ?></span></td>
                            <td style="max-width: 250px;" class="text-truncate">
                                <span class="fw-medium text-dark"><?= htmlspecialchars($m['TieuDe']) ?></span>
                            </td>
                            <td>
                                <?php if ($m['TrangThai'] == 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5" style="font-size: 12px;">Đã đọc</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3 py-1.5" style="font-size: 12px;">Chưa đọc</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="text-secondary"><?= date('d/m/Y H:i', strtotime($m['NgayTao'])) ?></span></td>
                            <td class="pe-4 text-end">
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#mailModal<?= $m['id'] ?>">
                                    <i class="far fa-eye me-1"></i>Xem
                                </button>
                            </td>
                        </tr>

                        <!-- MODAL CHI TIẾT NỘI DUNG THƯ -->
                        <div class="modal fade" id="mailModal<?= $m['id'] ?>" tabindex="-1" aria-labelledby="mailModalLabel<?= $m['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold text-dark" id="mailModalLabel<?= $m['id'] ?>">Chi tiết thư đã gửi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body py-4">
                                        <div class="mb-3">
                                            <span class="text-muted d-block" style="font-size: 12px; text-transform: uppercase;">Người nhận</span>
                                            <strong class="text-dark"><?= htmlspecialchars($m['HoTenKH'] ?? 'Không rõ') ?></strong> 
                                            <span class="text-muted">(<?= htmlspecialchars($m['email'] ?? 'Không rõ') ?>)</span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="text-muted d-block" style="font-size: 12px; text-transform: uppercase;">Ngày gửi</span>
                                            <span class="text-dark"><?= date('d/m/Y H:i:s', strtotime($m['NgayTao'])) ?></span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="text-muted d-block" style="font-size: 12px; text-transform: uppercase;">Tiêu đề</span>
                                            <strong class="text-dark"><?= htmlspecialchars($m['TieuDe']) ?></strong>
                                        </div>
                                        <hr class="my-3 text-muted">
                                        <div>
                                            <span class="text-muted d-block mb-2" style="font-size: 12px; text-transform: uppercase;">Nội dung thư</span>
                                            <div class="p-3 bg-light rounded" style="font-size: 14px; line-height: 1.6; white-space: pre-line;">
                                                <?= nl2br(htmlspecialchars($m['NoiDung'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Đóng</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="far fa-envelope-open fa-3x mb-3 text-secondary"></i>
                                <p class="mb-0">Chưa có email hay thông báo nào được gửi.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
