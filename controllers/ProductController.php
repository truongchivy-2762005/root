<?php

/**
 * ProductController - Handles product-related pages
 * Include model trước, sau đó include header -> view content -> footer
 */
class ProductController {

    public function danhSach() {
        $tieuDeTrang = 'Sản phẩm - VAB Thời Trang Thể Thao';
        $trangHienTai = 'products';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        include './views/giaodien/dautrang.php';
        include './views/giaodien/sanpham.php';
        include './views/giaodien/chantrang.php';
    }

    public function chiTiet() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?page=products');
            exit;
        }

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();
        $product = $productModel->getById((int) $id);

        if (!$product || (int)$product['TrangThai'] !== 1) {
            $tieuDeTrang = 'Không tìm thấy sản phẩm - VAB';
            $trangHienTai = 'products';
            $nhungTuController = true;

            include './views/giaodien/dautrang.php';
            echo '<section class="py-5"><div class="container text-center"><h2>Không tìm thấy sản phẩm</h2><p class="text-muted mt-2">Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.</p><a href="index.php?page=products" class="btn btn-secondary rounded-pill mt-3 px-4" style="background:var(--color-secondary);border:none;">Quay lại danh sách</a></div></section>';
            include './views/giaodien/chantrang.php';
            return;
        }

        $tieuDeTrang = $product['TenSP'] . ' - VAB';
        $trangHienTai = 'products';
        $nhungTuController = true;
        $relatedProducts = $productModel->getNewProducts(4);

        // Nạp Model Đánh giá và lấy dữ liệu
        if (!class_exists('ReviewModel')) {
            require './models/ReviewModel.php';
        }
        $reviewModel = new ReviewModel();
        $approvedReviews = $reviewModel->getApprovedReviewsByProduct((int)$id);
        $avgRating = $reviewModel->getAverageRating((int)$id);
        $reviewCount = $reviewModel->getReviewCount((int)$id);
        
        $userId = $_SESSION['ma_nguoidung'] ?? null;
        $canReview = false;
        $hasReviewed = false;
        if ($userId) {
            $canReview = $reviewModel->canUserReview($userId, (int)$id);
            $hasReviewed = $reviewModel->hasUserReviewed($userId, (int)$id);
        }

        $productSizes = $productModel->getSizes((int)$id);

        include './views/giaodien/dautrang.php';
        include './views/giaodien/chitiet-sanpham.php';
        include './views/giaodien/chantrang.php';
    }

    public function themGioHang() {
        $id = $_GET['id'] ?? null;
        $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
        if ($qty < 1) $qty = 1;
        $size = isset($_GET['size']) ? trim($_GET['size']) : '';

        if (!$id) {
            if (isset($_GET['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
                exit;
            }
            header('Location: index.php?page=products');
            exit;
        }

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();
        $product = $productModel->getById((int) $id);

        if (!$product || (int)$product['TrangThai'] !== 1) {
            if (isset($_GET['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại hoặc đã bị ẩn.']);
                exit;
            }
            header('Location: index.php?page=products');
            exit;
        }

        // Lấy tồn kho thực tế của size
        $productSizes = $productModel->getSizes((int)$id);
        $availableStock = 0;
        $sizeExists = false;

        if (!empty($productSizes)) {
            foreach ($productSizes as $ps) {
                if ($ps['KichCo'] === $size) {
                    $availableStock = (int)$ps['SoLuong'];
                    $sizeExists = true;
                    break;
                }
            }
            if (!$sizeExists && !empty($size)) {
                if (isset($_GET['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Kích thước không hợp lệ.']);
                    exit;
                }
                header('Location: index.php?page=product-detail&id=' . (int)$id);
                exit;
            }
            if (empty($size)) {
                if (isset($_GET['ajax'])) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Vui lòng chọn kích thước.']);
                    exit;
                }
                header('Location: index.php?page=product-detail&id=' . (int)$id);
                exit;
            }
        } else {
            $availableStock = 0; // Kích cỡ không tồn tại đồng nghĩa hết hàng
        }

        if (!isset($_SESSION['giohang'])) {
            $_SESSION['giohang'] = [];
        }

        $price = ProductModel::getDisplayPrice($product);
        $pid = (int) $product['MaSP'];
        $cartKey = !empty($size) ? $pid . '_' . $size : $pid;

        // Kiểm tra cộng dồn số lượng có vượt quá tồn kho hay không
        $currentCartQty = isset($_SESSION['giohang'][$cartKey]) ? (int)$_SESSION['giohang'][$cartKey]['SoLuongSP'] : 0;
        if ($currentCartQty + $qty > $availableStock) {
            $remainingAllowed = $availableStock - $currentCartQty;
            if ($remainingAllowed <= 0) {
                $msg = 'Bạn đã thêm tối đa số lượng sản phẩm này trong giỏ hàng (' . $availableStock . ' sản phẩm).';
            } else {
                $msg = 'Rất tiếc, bạn chỉ có thể thêm tối đa ' . $remainingAllowed . ' sản phẩm cho kích thước này vào giỏ hàng.';
            }
            if (isset($_GET['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $msg]);
                exit;
            }
            header('Location: index.php?page=product-detail&id=' . $pid);
            exit;
        }

        if (isset($_SESSION['giohang'][$cartKey])) {
            $_SESSION['giohang'][$cartKey]['SoLuongSP'] += $qty;
        } else {
            $_SESSION['giohang'][$cartKey] = [
                'MaSP'      => $pid,
                'TenSP'     => $product['TenSP'],
                'KichCo'    => $size,
                'DonGia'    => $price,
                'AnhChinh'  => ProductModel::getImageUrl($product, 80, 80),
                'SoLuongSP' => $qty,
            ];
        }

        // Tính tổng số lượng trong giỏ hàng để cập nhật Badge
        $cartCount = 0;
        if (!empty($_SESSION['giohang'])) {
            foreach ($_SESSION['giohang'] as $item) {
                $cartCount += (int)($item['SoLuongSP'] ?? 0);
            }
        }

        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng thành công!',
                'cartCount' => $cartCount
            ]);
            exit;
        }

        header('Location: index.php?page=cart');
        exit;
    }

    public function nam() {
        $tieuDeTrang = 'Thời trang nam - VAB';
        $trangHienTai = 'men';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/thoitrangnam.php';
        include './views/giaodien/chantrang.php';
    }

    public function nu() {
        $tieuDeTrang = 'Thời trang nữ - VAB';
        $trangHienTai = 'women';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/thoitrangnu.php';
        include './views/giaodien/chantrang.php';
    }

    public function giamGia() {
        $tieuDeTrang = 'Sale - VAB';
        $trangHienTai = 'sale';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/giamgia.php';
        include './views/giaodien/chantrang.php';
    }

    public function theThaoChayBo() {
        $tieuDeTrang = 'Chạy bộ - VAB';
        $trangHienTai = 'sport-running';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/chaybo.php';
        include './views/giaodien/chantrang.php';
    }

    public function theThaoGym() {
        $tieuDeTrang = 'Gym - VAB';
        $trangHienTai = 'sport-gym';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/gym.php';
        include './views/giaodien/chantrang.php';
    }

    public function theThaoBongDa() {
        $tieuDeTrang = 'Bóng đá - VAB';
        $trangHienTai = 'sport-football';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/bongda.php';
        include './views/giaodien/chantrang.php';
    }

    public function theThaoBongRo() {
        $tieuDeTrang = 'Bóng rổ - VAB';
        $trangHienTai = 'sport-basketball';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/bongro.php';
        include './views/giaodien/chantrang.php';
    }

    public function theThaoTennis() {
        $tieuDeTrang = 'Tennis - VAB';
        $trangHienTai = 'sport-tennis';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/tennis.php';
        include './views/giaodien/chantrang.php';
    }

    public function thuongHieuNike() {
        $tieuDeTrang = 'Nike - VAB Thời Trang Thể Thao';
        $trangHienTai = 'brand-nike';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/nike.php';
        include './views/giaodien/chantrang.php';
    }

    public function thuongHieuAdidas() {
        $tieuDeTrang = 'Adidas - VAB Thời Trang Thể Thao';
        $trangHienTai = 'brand-adidas';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/adidas.php';
        include './views/giaodien/chantrang.php';
    }

    public function thuongHieuPuma() {
        $tieuDeTrang = 'Puma - VAB Thời Trang Thể Thao';
        $trangHienTai = 'brand-puma';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/puma.php';
        include './views/giaodien/chantrang.php';
    }

    public function timKiem() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        if (empty($keyword)) {
            header('Location: index.php?page=products');
            exit;
        }
        $tieuDeTrang = 'Tìm kiếm: ' . htmlspecialchars($keyword) . ' - VAB';
        $trangHienTai = 'search';
        $nhungTuController = true;
        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();
        $products = $productModel->search($keyword);
        $totalResults = count($products);
        include './views/giaodien/dautrang.php';
        include './views/giaodien/ketqua-timkiem.php';
        include './views/giaodien/chantrang.php';
    }

    public function taiThem() {
        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
        $limit = 12;
        $products = $productModel->getWithOffset($offset, $limit);
        $total = $productModel->getCount();
        $loaded = $offset + count($products);
        $hasMore = $loaded < $total;
        include './views/giaodien/partials/product-items.php';
    }

    public function gioHang() {
        $tieuDeTrang = 'Giỏ hàng - VAB';
        $trangHienTai = 'cart';
        $nhungTuController = true;
        $cartItems = $_SESSION['giohang'] ?? [];
        include './views/giaodien/dautrang.php';
        include './views/giaodien/giohang.php';
        include './views/giaodien/chantrang.php';
    }

    public function capNhatGioHang() {
        $key = isset($_GET['id']) ? trim($_GET['id']) : '';
        $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
        if (!empty($key) && isset($_SESSION['giohang'][$key])) {
            if ($qty > 0) {
                // Phân tích key để lấy product_id và size
                $parts = explode('_', $key);
                $pid = (int)$parts[0];
                $size = isset($parts[1]) ? trim($parts[1]) : '';

                if (!class_exists('ProductModel')) {
                    require './models/ProductModel.php';
                }
                $productModel = new ProductModel();
                $product = $productModel->getById($pid);

                if ($product) {
                    $productSizes = $productModel->getSizes($pid);
                    $availableStock = 0;
                    $sizeExists = false;

                    if (!empty($productSizes)) {
                        foreach ($productSizes as $ps) {
                            if ($ps['KichCo'] === $size) {
                                $availableStock = (int)$ps['SoLuong'];
                                $sizeExists = true;
                                break;
                            }
                        }
                    } else {
                        $availableStock = 0;
                    }

                    if ($qty > $availableStock) {
                        $qty = $availableStock; // Giới hạn lại số lượng bằng tồn kho tối đa
                    }
                }

                $_SESSION['giohang'][$key]['SoLuongSP'] = $qty;
            } else {
                unset($_SESSION['giohang'][$key]);
            }
        }
        header('Location: index.php?page=cart');
        exit;
    }

    public function xoaGioHang() {
        $key = isset($_GET['id']) ? trim($_GET['id']) : '';
        if (!empty($key) && isset($_SESSION['giohang'][$key])) {
            unset($_SESSION['giohang'][$key]);
        }
        header('Location: index.php?page=cart');
        exit;
    }

    public function thanhToan() {
        if (!isset($_SESSION['ma_nguoidung'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!class_exists('OrderModel')) {
                require './models/OrderModel.php';
            }
            $orderModel = new OrderModel();
            $cartItems = $_SESSION['giohang'] ?? [];
            if (empty($cartItems)) {
                header('Location: index.php?page=cart');
                exit;
            }

            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['DonGia'] * $item['SoLuongSP'];
            }

            // Coupon calculation and validation
            $couponCode = null;
            $discount = 0;
            if (isset($_SESSION['applied_coupon'])) {
                $sessionCoupon = $_SESSION['applied_coupon'];
                $code = $sessionCoupon['code'];
                
                $pdo = getConnection();
                $stmt = $pdo->prepare("SELECT * FROM MAGIAMGIA WHERE MaGiamGia = ? LIMIT 1");
                $stmt->execute([$code]);
                $coupon = $stmt->fetch();
                
                if ($coupon) {
                    $now = date('Y-m-d');
                    if ($coupon['NgayBD'] <= $now && 
                        $coupon['NgayKT'] >= $now && 
                        $coupon['TrangThai'] === 'active' && 
                        $coupon['DaDung'] < $coupon['SoLuot'] && 
                        $total >= $coupon['DonHangToiThieu']) {
                        
                        $couponCode = $coupon['MaGiamGia'];
                        if ($coupon['Loai'] === 'percent') {
                            $discount = round($total * ($coupon['GiaTri'] / 100));
                        } else {
                            $discount = (int)$coupon['GiaTri'];
                        }
                        if ($discount > $total) {
                            $discount = $total;
                        }
                    }
                }
            }

            $finalTotal = $total - $discount;
            $paymentMethod = $_POST['payment_method'] ?? 'COD';
            // Tất cả đơn hàng mới đều bắt đầu ở trạng thái 'pending' (Chờ xác nhận / Chờ thanh toán)
            $initialStatus = 'pending';

            $orderId = $orderModel->create([
                'MaKH'         => $_SESSION['ma_nguoidung'],
                'HoTenKH'       => $_POST['fullname'] ?? '',
                'email'          => $_POST['email'] ?? '',
                'SDT'          => $_POST['phone'] ?? '',
                'DiaChi'        => $_POST['address'] ?? '',
                'PhuongThucTT' => $paymentMethod,
                'GhiChu'           => $_POST['note'] ?? '',
                'TongTien'          => $finalTotal,
                'TrangThaiDH'         => $initialStatus,
                'MaGiamGia'        => $couponCode,
                'SoTienGiam'       => $discount
            ]);

            $orderModel->addItems($orderId, $cartItems);

            // Increment coupon use count and clear session coupon on successful order placement
            if ($orderId > 0) {
                if ($couponCode !== null) {
                    $pdo = getConnection();
                    $stmt = $pdo->prepare("UPDATE MAGIAMGIA SET DaDung = DaDung + 1 WHERE MaGiamGia = ?");
                    $stmt->execute([$couponCode]);
                }
                unset($_SESSION['applied_coupon']);
            }

            // Giảm số lượng tồn kho của các sản phẩm đã đặt
            if (!class_exists('ProductModel')) {
                require './models/ProductModel.php';
            }
            $productModel = new ProductModel();
            foreach ($cartItems as $item) {
                if (!empty($item['MaSP'])) {
                    $productModel->reduceStock((int)$item['MaSP'], (int)$item['SoLuongSP'], $item['KichCo'] ?? '');
                }
            }

            // Gửi email xác nhận đơn hàng (CHỈ với COD và phương thức không phải SePay)
            if (!empty($_POST['email']) && $paymentMethod !== 'sepay') {
                if (!function_exists('sendMail')) {
                    require './helpers/mail_helper.php';
                }

                // 1. Tính toán base URL tuyệt đối cho hình ảnh
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
                if (substr($baseDir, -1) !== '/') {
                    $baseDir .= '/';
                }
                $absoluteBaseUrl = $protocol . '://' . $host . $baseDir;

                // 2. Tạo danh sách sản phẩm dạng HTML table row
                $productItemsRows = '';
                foreach ($cartItems as $item) {
                    $imgUrl = $item['AnhChinh'];
                    if (strpos($imgUrl, 'http://') !== 0 && strpos($imgUrl, 'https://') !== 0) {
                        $imgUrl = $absoluteBaseUrl . ltrim($imgUrl, './');
                    }
                    
                    $productItemsRows .= '
                    <tr>
                        <td width="60" style="padding-bottom: 15px; vertical-align: top;">
                            <img src="' . htmlspecialchars($imgUrl) . '" width="60" height="60" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" alt="' . htmlspecialchars($item['TenSP']) . '">
                        </td>
                        <td style="padding-left: 15px; padding-bottom: 15px; vertical-align: top;">
                            <div style="font-size: 14px; font-weight: 600; color: #ffffff; margin-bottom: 4px;">' . htmlspecialchars($item['TenSP']) . '</div>';
                    if (!empty($item['KichCo'])) {
                        $productItemsRows .= '
                            <div style="font-size: 12px; color: #aaaaaa;">Size: ' . htmlspecialchars($item['KichCo']) . '</div>';
                    }
                    $productItemsRows .= '
                        </td>
                        <td align="right" style="padding-bottom: 15px; vertical-align: top; font-size: 14px; color: #ffffff; white-space: nowrap;">
                            ' . number_format($item['DonGia'], 0, ',', '.') . 'đ<br>
                            <small style="color: #aaaaaa; font-size: 12px;">x ' . $item['SoLuongSP'] . '</small>
                        </td>
                    </tr>';
                }

                // 3. Định dạng ngày đặt hàng và các chi phí
                $orderDate = date('d/m/Y H:i');
                $subtotalFormatted = number_format($total, 0, ',', '.');
                $discountRowEmail = '';
                if ($discount > 0) {
                    $discountRowEmail = '
                            <tr>
                                <td style="padding-bottom: 8px;">Mã giảm giá (' . htmlspecialchars($couponCode) . ')</td>
                                <td align="right" style="padding-bottom: 8px; color: #ffc107;">-' . number_format($discount, 0, ',', '.') . 'đ</td>
                            </tr>';
                }
                $totalFormatted = number_format($finalTotal, 0, ',', '.');
                $orderDetailUrl = $absoluteBaseUrl . 'index.php?page=order-detail&id=' . $orderId;

                // 4. Mẫu HTML hoàn chỉnh
                $emailBody = '
                <div style="background-color: #121212; padding: 30px 15px; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; color: #ffffff; line-height: 1.6; max-width: 600px; margin: 0 auto; border-radius: 12px;">
                    <!-- Brand Header -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px; border-bottom: 1px solid #2a2a2a; padding-bottom: 15px;">
                        <tr>
                            <td align="center">
                                <h2 style="margin: 0; color: #f25c19; font-size: 24px; font-weight: 800; letter-spacing: 1px;">VAB SHOP</h2>
                                <span style="font-size: 10px; color: #888888; text-transform: uppercase; letter-spacing: 2px;">Thời Trang Thể Thao Cao Cấp</span>
                            </td>
                        </tr>
                    </table>
                    
                    <!-- Message -->
                    <div style="margin-bottom: 25px;">
                        <h3 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 600; color: #ffffff;">Cảm ơn bạn đã đặt hàng!</h3>
                        <p style="margin: 0; color: #aaaaaa; font-size: 14px;">Đơn đặt hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
                    </div>
                    
                    <!-- Product List -->
                    <div style="background-color: #1a1a1a; padding: 15px 15px 0 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #2a2a2a;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            ' . $productItemsRows . '
                        </table>
                    </div>

                    <!-- Order Metadata -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 20px; font-size: 13px; color: #aaaaaa;">
                        <tr>
                            <td style="padding-bottom: 6px;">ID đơn hàng</td>
                            <td align="right" style="padding-bottom: 6px; color: #ffffff; font-weight: 600;">#' . $orderId . '</td>
                        </tr>
                        <tr>
                            <td>Ngày đặt hàng</td>
                            <td align="right" style="color: #ffffff;">' . $orderDate . '</td>
                        </tr>
                    </table>
                    
                    <!-- Order Summary -->
                    <div style="background-color: #1a1a1a; padding: 15px; border-radius: 10px; margin-bottom: 25px; border: 1px solid #2a2a2a;">
                        <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 600; color: #ffffff; border-bottom: 1px solid #2a2a2a; padding-bottom: 8px;">Tổng quan đơn hàng</h4>
                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; color: #aaaaaa;">
                            <tr>
                                <td style="padding-bottom: 8px;">Tổng phụ</td>
                                <td align="right" style="padding-bottom: 8px; color: #ffffff;">' . $subtotalFormatted . 'đ</td>
                            </tr>
                            ' . $discountRowEmail . '
                            <tr>
                                <td style="padding-bottom: 8px;">Vận chuyển</td>
                                <td align="right" style="padding-bottom: 8px; color: #28a745; font-weight: 600;">Miễn phí</td>
                            </tr>
                            <tr style="font-size: 16px; font-weight: bold;">
                                <td style="padding-top: 8px; border-top: 1px solid #2a2a2a; color: #ffffff;">Tổng cộng</td>
                                <td align="right" style="padding-top: 8px; border-top: 1px solid #2a2a2a; color: #f25c19;">' . $totalFormatted . 'đ</td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Action Button -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                        <tr>
                            <td align="center">
                                <a href="' . htmlspecialchars($orderDetailUrl) . '" style="display: inline-block; background-color: #df3852; color: #ffffff; text-decoration: none; padding: 12px 30px; border-radius: 50px; font-size: 14px; font-weight: 600;">Xem chi tiết đơn hàng</a>
                            </td>
                        </tr>
                    </table>
                    
                    <!-- Shipping Address -->
                    <div style="background-color: #1a1a1a; padding: 15px; border-radius: 10px; border: 1px solid #2a2a2a; font-size: 14px;">
                        <h4 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 600; color: #ffffff; border-bottom: 1px solid #2a2a2a; padding-bottom: 8px;">Địa chỉ vận chuyển</h4>
                        <p style="margin: 0 0 6px 0; color: #ffffff; font-weight: 600;">' . htmlspecialchars($_POST['fullname']) . '</p>
                        <p style="margin: 0 0 6px 0; color: #aaaaaa;">' . htmlspecialchars($_POST['phone']) . '</p>
                        <p style="margin: 0; color: #aaaaaa; font-size: 13px; line-height: 1.4;">' . htmlspecialchars($_POST['address']) . '</p>
                    </div>
                    
                    <!-- Footer info -->
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 30px; font-size: 11px; color: #555555; border-top: 1px solid #2a2a2a; padding-top: 15px;">
                        <tr>
                            <td align="center">
                                <p style="margin: 0 0 5px 0;">Email này được gửi tự động từ hệ thống cửa hàng VAB SHOP.</p>
                                <p style="margin: 0;">&copy; 2026 VAB SHOP. All rights reserved.</p>
                            </td>
                        </tr>
                    </table>
                </div>';

                $subject = "Xác nhận đơn hàng #" . $orderId . " từ VAB SHOP";
                
                // Gửi mail bằng helper
                sendMail($_POST['email'], $_POST['fullname'], $subject, $emailBody);
            }

            // Clear cart
            unset($_SESSION['giohang']);

            // Redirect: SePay → trang QR thanh toán, các phương thức khác → trang đơn hàng
            if ($paymentMethod === 'sepay') {
                header('Location: index.php?page=sepay-payment&order_id=' . $orderId);
            } else {
                header('Location: index.php?page=orders&msg=order_placed');
            }
            exit;
        }

        $tieuDeTrang = 'Thanh toán - VAB';
        $trangHienTai = 'checkout';
        $nhungTuController = true;
        include './views/giaodien/dautrang.php';
        include './views/giaodien/thanhtoan.php';
        include './views/giaodien/chantrang.php';
    }

    public function thanhtoanSepay() {
        if (!isset($_SESSION['ma_nguoidung'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        if ($orderId <= 0) {
            header('Location: index.php?page=orders');
            exit;
        }

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $orderModel = new OrderModel();
        $userId = $_SESSION['ma_nguoidung'];
        $order = $orderModel->getOrderByIdAndUserId($orderId, $userId);

        if (!$order) {
            header('Location: index.php?page=orders');
            exit;
        }

        $tieuDeTrang = 'Thanh toán chuyển khoản - VAB';
        $trangHienTai = 'orders';
        $nhungTuController = true;

        include './views/giaodien/dautrang.php';
        include './views/giaodien/thanhtoan-sepay.php';
        include './views/giaodien/chantrang.php';
    }

    public function kiemtraTrangthaiSepay() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['ma_nguoidung'])) {
            echo json_encode(['status' => 'unauthorized']);
            exit;
        }

        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        if ($orderId <= 0) {
            echo json_encode(['status' => 'invalid']);
            exit;
        }

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $orderModel = new OrderModel();
        $userId = $_SESSION['ma_nguoidung'];
        $order = $orderModel->getOrderByIdAndUserId($orderId, $userId);

        if (!$order) {
            echo json_encode(['status' => 'not_found']);
            exit;
        }

        $rawStatus = trim(strtolower($order['TrangThaiDH']));
        $status = $rawStatus;
        if ($rawStatus === 'chờ xác nhận' || $rawStatus === 'cho xac nhan') {
            $status = 'pending';
        } elseif ($rawStatus === 'đã xác nhận' || $rawStatus === 'da xac nhan') {
            $status = 'confirmed';
        } elseif ($rawStatus === 'đang giao hàng' || $rawStatus === 'dang giao hang') {
            $status = 'shipping';
        } elseif ($rawStatus === 'đã giao hàng' || $rawStatus === 'da giao hang' || $rawStatus === 'hoàn thành' || $rawStatus === 'hoan thanh' || $rawStatus === 'completed') {
            $status = 'completed';
        }

        echo json_encode(['status' => $status]);
        exit;
    }

    public function apDungMaGiamGia() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['ma_nguoidung'])) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập để sử dụng mã giảm giá.']);
            exit;
        }

        $code = strtoupper(trim($_POST['code'] ?? $_GET['code'] ?? ''));
        if (empty($code)) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập mã giảm giá.']);
            exit;
        }

        $cartItems = $_SESSION['giohang'] ?? [];
        if (empty($cartItems)) {
            echo json_encode(['status' => 'error', 'message' => 'Giỏ hàng của bạn đang trống.']);
            exit;
        }

        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $cartTotal += $item['DonGia'] * $item['SoLuongSP'];
        }

        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM MAGIAMGIA WHERE MaGiamGia = ? LIMIT 1");
        $stmt->execute([$code]);
        $coupon = $stmt->fetch();

        if (!$coupon) {
            echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá không tồn tại.']);
            exit;
        }

        $now = date('Y-m-d');
        if ($coupon['NgayBD'] > $now) {
            echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá chưa đến thời gian áp dụng.']);
            exit;
        }

        if ($coupon['NgayKT'] < $now) {
            echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá đã hết hạn sử dụng.']);
            exit;
        }

        if ($coupon['TrangThai'] !== 'active') {
            echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá đã bị khóa hoặc không hoạt động.']);
            exit;
        }

        if ($coupon['DaDung'] >= $coupon['SoLuot']) {
            echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá đã hết lượt sử dụng.']);
            exit;
        }

        if ($cartTotal < $coupon['DonHangToiThieu']) {
            echo json_encode([
                'status' => 'error', 
                'message' => 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($coupon['DonHangToiThieu'], 0, ',', '.') . 'đ để sử dụng mã này.'
            ]);
            exit;
        }

        // Calculate discount
        $discount = 0;
        if ($coupon['Loai'] === 'percent') {
            $discount = round($cartTotal * ($coupon['GiaTri'] / 100));
        } else {
            $discount = (int)$coupon['GiaTri'];
        }

        // Discount cannot be larger than the cart total itself
        if ($discount > $cartTotal) {
            $discount = $cartTotal;
        }

        // Store coupon in session - overwrites any existing coupon (strict 1 coupon constraint)
        $_SESSION['applied_coupon'] = [
            'code' => $coupon['MaGiamGia'],
            'discount' => $discount,
            'type' => $coupon['Loai'],
            'value' => $coupon['GiaTri']
        ];

        echo json_encode([
            'status' => 'success',
            'code' => $coupon['MaGiamGia'],
            'discount' => $discount,
            'discountFormatted' => number_format($discount, 0, ',', '.') . 'đ',
            'newTotal' => $cartTotal - $discount,
            'newTotalFormatted' => number_format($cartTotal - $discount, 0, ',', '.') . 'đ',
            'message' => 'Áp dụng mã giảm giá thành công!'
        ]);
        exit;
    }

    public function xoaMaGiamGia() {
        header('Content-Type: application/json');
        unset($_SESSION['applied_coupon']);
        
        $cartItems = $_SESSION['giohang'] ?? [];
        $cartTotal = 0;
        foreach ($cartItems as $item) {
            $cartTotal += $item['DonGia'] * $item['SoLuongSP'];
        }

        echo json_encode([
            'status' => 'success',
            'newTotal' => $cartTotal,
            'newTotalFormatted' => number_format($cartTotal, 0, ',', '.') . 'đ',
            'message' => 'Đã hủy áp dụng mã giảm giá!'
        ]);
        exit;
    }

    public function chiTietThuongHieuDynamic() {
        $tieuDeTrang = 'Thương hiệu - VAB';
        $trangHienTai = 'brand-' . ($_GET['brand_slug'] ?? '');
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();

        include './views/giaodien/dautrang.php';
        include './views/giaodien/thuonghieu.php';
        include './views/giaodien/chantrang.php';
    }

    public function chiTietTheThaoDynamic() {
        $tieuDeTrang = 'Thể thao - VAB';
        $trangHienTai = $_GET['sport_slug'] ?? '';
        $nhungTuController = true;

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        $productModel = new ProductModel();

        include './views/giaodien/dautrang.php';
        include './views/giaodien/thethao.php';
        include './views/giaodien/chantrang.php';
    }
}
