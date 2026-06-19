<?php
if (!isset($nhungTuController)) {
    $tieuDeTrang = 'Chi tiết đơn hàng - VAB';
    $trangHienTai = 'orders';
    include 'dautrang.php';
}

if (!class_exists('ProductModel')) {
    include './models/ProductModel.php';
}

$statusInfo = OrderController::getStatusInfo($order['TrangThaiDH']);
$orderCode = '#ORD' . str_pad($order['MaDonHang'], 5, '0', STR_PAD_LEFT);
$formattedDate = date('d/m/Y H:i', strtotime($order['NgayTao']));
$totalPrice = number_format($order['TongTien'], 0, ',', '.') . 'đ';
?>

<!-- BREADCRUMB -->
<section class="breadcrumb-section">
    <div class="container">
        <h1>CHI TIẾT ĐƠN HÀNG</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php?page=home">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="index.php?page=orders">Đơn hàng của tôi</a></li>
                <li class="breadcrumb-item active">Chi tiết đơn hàng</li>
            </ol>
        </nav>
    </div>
</section>

<section class="pb-5">
    <div class="container">
        <div class="row">
            <!-- Thông tin đơn hàng & Người nhận -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 rounded-3 mb-4 bg-white">
                    <div class="card-header bg-light py-3 border-0">
                        <h5 class="fw-bold mb-0 text-primary-color"><i class="fas fa-info-circle me-2 text-secondary-color"></i>Thông tin chung</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-muted">Mã đơn hàng:</span>
                            <strong class="text-dark"><?= htmlspecialchars($orderCode) ?></strong>
                        </div>
                        <div class="mb-3 d-flex justify-content-between">
                            <span class="text-muted">Ngày đặt:</span>
                            <span class="text-dark"><?= htmlspecialchars($formattedDate) ?></span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Trạng thái:</span>
                            <span class="status-badge <?= htmlspecialchars($statusInfo['class']) ?>">
                                <?= htmlspecialchars($statusInfo['label']) ?>
                            </span>
                        </div>
                        <div class="mb-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Tổng thanh toán:</span>
                            <strong class="text-danger fs-5"><?= htmlspecialchars($totalPrice) ?></strong>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-header bg-light py-3 border-0">
                        <h5 class="fw-bold mb-0 text-primary-color"><i class="fas fa-shipping-fast me-2 text-secondary-color"></i>Thông tin nhận hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="text-muted mb-1 d-block">Người nhận hàng:</label>
                            <strong class="text-dark"><?= htmlspecialchars($order['HoTenKH']) ?></strong>
                        </div>
                        <div class="mb-3">
                            <label class="text-muted mb-1 d-block">Số điện thoại:</label>
                            <span class="text-dark"><?= htmlspecialchars($order['SDT']) ?></span>
                        </div>
                        <?php if (!empty($order['email'])): ?>
                            <div class="mb-3">
                                <label class="text-muted mb-1 d-block">Email:</label>
                                <span class="text-dark"><?= htmlspecialchars($order['email']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="text-muted mb-1 d-block">Địa chỉ giao hàng:</label>
                            <span class="text-dark d-block" style="line-height: 1.4;"><?= htmlspecialchars($order['DiaChi']) ?></span>
                        </div>
                        <?php if (!empty($order['PhuongThucTT'])): ?>
                            <div class="mb-3">
                                <label class="text-muted mb-1 d-block">Phương thức thanh toán:</label>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded">
                                    <?= htmlspecialchars($order['PhuongThucTT'] === 'COD' ? 'Thanh toán COD' : ($order['PhuongThucTT'] === 'Bank' ? 'Chuyển khoản ngân hàng' : 'Ví điện tử')) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($order['GhiChu'])): ?>
                            <div class="mb-0">
                                <label class="text-muted mb-1 d-block">Ghi chú đơn hàng:</label>
                                <em class="text-dark-gray text-muted" style="font-size: 13.5px;">"<?= htmlspecialchars($order['GhiChu']) ?>"</em>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm mua -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3 bg-white p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0 text-primary-color"><i class="fas fa-shopping-bag me-2 text-secondary-color"></i>Sản phẩm đã mua</h5>
                        <a href="index.php?page=orders" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                            <i class="fas fa-chevron-left me-1"></i>Danh sách đơn hàng
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle cart-table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="py-3 px-4">Sản phẩm</th>
                                    <th scope="col" class="py-3 text-end" style="width: 20%">Đơn giá</th>
                                    <th scope="col" class="py-3 text-center" style="width: 15%">Số lượng</th>
                                    <th scope="col" class="py-3 text-end" style="width: 20%">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): 
                                    $itemSubtotal = $item['SoLuongSP'] * $item['DonGia'];
                                    $productDummy = [
                                        'MaSP' => $item['MaSP'],
                                        'AnhChinh' => $item['AnhChinh'] ?? ''
                                    ];
                                    $imageUrl = ProductModel::getImageUrl($productDummy, 80, 80);
                                ?>
                                    <tr>
                                        <td data-label="Sản phẩm" class="px-4">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= htmlspecialchars($imageUrl) ?>" alt="<?= htmlspecialchars($item['TenSP']) ?>" style="width: 65px; height: 65px; object-fit: cover; border-radius: 8px; margin-right: 15px;" class="border">
                                                <div>
                                                    <span class="fw-bold text-dark d-block" style="font-size: 14.5px; line-height: 1.3;"><?= htmlspecialchars($item['TenSP']) ?></span>
                                                    <div class="d-flex flex-wrap gap-2 align-items-center mt-1">
                                                        <small class="text-muted">Mã SP: #<?= (int)$item['MaSP'] ?></small>
                                                        <?php if (!empty($item['KichCo'])): ?>
                                                            <span class="badge bg-secondary text-white rounded-pill px-2 py-0.5" style="font-size: 11.5px;">Size: <?= htmlspecialchars($item['KichCo']) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Đơn giá" class="text-end text-dark font-monospace"><?= number_format($item['DonGia'], 0, ',', '.') ?>đ</td>
                                        <td data-label="Số lượng" class="text-center text-dark fw-semibold"><?= (int)$item['SoLuongSP'] ?></td>
                                        <td data-label="Thành tiền" class="text-end text-danger fw-bold font-monospace"><?= number_format($itemSubtotal, 0, ',', '.') ?>đ</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light border-top">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold py-3 text-primary-color">Tạm tính:</td>
                                    <td class="text-end fw-bold py-3 text-dark"><?= $totalPrice ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold py-3 text-primary-color border-0">Phí giao hàng:</td>
                                    <td class="text-end fw-bold py-3 text-success border-0">Miễn phí</td>
                                </tr>
                                <tr class="fs-5 border-top border-dark-subtle">
                                    <td colspan="3" class="text-end fw-extrabold py-3 text-primary-color">Tổng thanh toán:</td>
                                    <td class="text-end fw-extrabold py-3 text-danger"><?= $totalPrice ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!isset($nhungTuController)) { include 'chantrang.php'; } ?>
