<?php

/**
 * AdminController - Quản lý tất cả trang admin
 * Quản lý: Sản phẩm, Nhân viên, Đánh giá, Khách hàng, Voucher, Đơn hàng, Doanh thu
 */
class AdminController
{

    private function checkAuth()
    {
        if (!isset($_SESSION['ma_nhanvien'])) {
            header('Location: index.php?page=admin-login');
            exit;
        }
    }

    // ============ DASHBOARD ============
    public function trangChu()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Bảng điều khiển - VAB Admin';
        $trangHienTai = 'admin-dashboard';
        $nhungTuController = true;

        $pdo = getConnection();

        $stmt = $pdo->query("SELECT COUNT(*) FROM SANPHAM");
        $totalProducts = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM DONHANG");
        $totalOrders = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COUNT(*) FROM KHACHHANG");
        $totalUsers = $stmt->fetchColumn();

        $stmt = $pdo->query("SELECT COALESCE(SUM(TongTien), 0) FROM DONHANG WHERE TrangThaiDH='completed'");
        $totalRevenue = $stmt->fetchColumn();

        // Đơn hàng gần đây
        $stmt = $pdo->query("SELECT dh.*, kh.HoTenKH AS customer_name FROM DONHANG dh LEFT JOIN KHACHHANG kh ON dh.MaKH = kh.MaKH ORDER BY dh.NgayTao DESC LIMIT 5");
        $recentOrders = $stmt->fetchAll();

        // Sản phẩm tồn kho thấp (dưới 7 sản phẩm)
        $stmt = $pdo->query("SELECT sp.*, COALESCE(SUM(ql.SoLuong),0) AS tong_kho FROM SANPHAM sp LEFT JOIN QUANLYSP ql ON sp.MaSP = ql.MaSP GROUP BY sp.MaSP HAVING tong_kho > 0 AND tong_kho < 7 ORDER BY tong_kho ASC LIMIT 5");
        $lowStock = $stmt->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-trangchu.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ PRODUCTS ============
    public function sanPham()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý sản phẩm - VAB Admin';
        $trangHienTai = 'admin-products';
        $nhungTuController = true;

        $pdo = getConnection();

        // Xóa sản phẩm
        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $productId = (int)$_GET['delete'];

            // Kiểm tra xem sản phẩm đã được mua chưa
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM CHITIETDONHANG WHERE MaSP = ?");
            $stmtCheck->execute([$productId]);
            $hasPurchased = (int)$stmtCheck->fetchColumn() > 0;

            if ($hasPurchased) {
                header('Location: index.php?page=admin-products&msg=error_purchased');
                exit;
            } else {
                $stmt = $pdo->prepare("DELETE FROM SANPHAM WHERE MaSP = ?");
                $stmt->execute([$productId]);
                header('Location: index.php?page=admin-products&msg=deleted');
                exit;
            }
        }

        // Thay đổi trạng thái hiển thị
        if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
            $stmt = $pdo->prepare("UPDATE SANPHAM SET TrangThai = IF(TrangThai=1, 0, 1) WHERE MaSP = ?");
            $stmt->execute([$_GET['toggle_status']]);
            header('Location: index.php?page=admin-products&msg=updated');
            exit;
        }

        $search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
        $category = isset($_GET['category']) ? trim($_GET['category']) : '';

        $sql    = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand, COALESCE(SUM(ql.SoLuong),0) AS SoLuong 
                   FROM SANPHAM sp 
                   LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc 
                   LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                   LEFT JOIN QUANLYSP ql ON sp.MaSP = ql.MaSP 
                   WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql     .= " AND sp.TenSP LIKE ?";
            $params[] = '%' . $search . '%';
        }
        if ($category !== '') {
            if (is_numeric($category)) {
                $sql     .= " AND sp.MaDanhMuc = ?";
                $params[] = (int)$category;
            } else {
                $catMap = [
                    'Áo' => 1,
                    'Quần' => 2,
                    'Giày' => 3,
                    'Phụ kiện' => 4,
                    'Ao' => 1,
                    'Quan' => 2,
                    'Giay' => 3,
                    'Phu kien' => 4
                ];
                $catId = $catMap[$category] ?? 0;
                $sql     .= " AND sp.MaDanhMuc = ?";
                $params[] = $catId;
            }
        }

        $sql .= " GROUP BY sp.MaSP ORDER BY sp.NgayTao DESC";

        $stmt     = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        $categoriesList = $pdo->query("SELECT * FROM DANHMUCSP ORDER BY MaDanhMuc ASC")->fetchAll();

        $msg = $_GET['msg'] ?? '';

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-sanpham.php';
        include './views/quantri/quantri-chantrang.php';
    }
    // thêm sản phẩm
    public function themSanPham()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Thêm sản phẩm - VAB Admin';
        $trangHienTai = 'admin-product-add';
        $nhungTuController = true;

        $pdo = getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name      = $_POST['name'] ?? '';
            $slug      = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            $category  = $_POST['category'] ?? '';
            $brand     = $_POST['brand'] ?? '';
            $price     = (int)($_POST['price'] ?? 0);
            $sale_price = !empty($_POST['sale_price']) ? (int)$_POST['sale_price'] : null;
            $gender    = $_POST['gender'] ?? '';
            $sport     = $_POST['sport'] ?? '';
            $status    = (int)($_POST['status'] ?? 1);
            $description = $_POST['description'] ?? '';

            // Xử lý Size và Số lượng
            $inputSizes      = $_POST['sizes']      ?? [];
            $inputQuantities = $_POST['quantities']  ?? [];
            $sizeData  = [];
            $sizesCache = [];
            if (is_array($inputSizes) && is_array($inputQuantities)) {
                for ($i = 0; $i < count($inputSizes); $i++) {
                    $sz = trim($inputSizes[$i]);
                    if ($sz !== '') {
                        $qty = max(0, (int)($inputQuantities[$i] ?? 0));
                        $sizeData[$sz] = $qty;
                        $sizesCache[]  = $sz;
                    }
                }
            }
            $sizesStr = implode(',', $sizesCache);

            // Upload ảnh chính
            $image = '';
            if (!empty($_FILES['image']['name'])) {
                $target_dir = 'assets/images/products/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $image = time() . '_' . str_replace(',', '_', basename($_FILES['image']['name']));
                move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
            }

            // Upload ảnh phụ (sub_images)
            $sub_images_list = [];
            if (!empty($_FILES['sub_images']['name'][0])) {
                $target_dir = 'assets/images/products/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $file_count = count($_FILES['sub_images']['name']);
                $limit = min($file_count, 3);
                for ($i = 0; $i < $limit; $i++) {
                    if ($_FILES['sub_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $sub_img_name = time() . '_sub_' . $i . '_' . str_replace(',', '_', basename($_FILES['sub_images']['name'][$i]));
                        if (move_uploaded_file($_FILES['sub_images']['tmp_name'][$i], $target_dir . $sub_img_name)) {
                            $sub_images_list[] = $sub_img_name;
                        }
                    }
                }
            }
            $images_str = implode(',', $sub_images_list);

            $stmt = $pdo->prepare("INSERT INTO SANPHAM (TenSP, DuongDan, MaDanhMuc, MaThuongHieu, Gia, GiaKhuyenMai, GioiTinh, TheThao, TrangThai, KichCo, AnhChinh, AnhPhu, MoTaSP, NgayTao) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW())");
            $stmt->execute([$name, $slug, $category, $brand, $price, $sale_price, $gender, $sport, $status, $sizesStr, $image, $images_str, $description]);
            $productId = $pdo->lastInsertId();

            $stmtSize = $pdo->prepare("INSERT INTO QUANLYSP (MaSP, KichCo, SoLuong) VALUES (?, ?, ?)");
            foreach ($sizeData as $sz => $qty) {
                $stmtSize->execute([$productId, $sz, $qty]);
            }

            header('Location: index.php?page=admin-products&msg=added');
            exit;
        }

        $categories = $pdo->query("SELECT * FROM DANHMUCSP")->fetchAll();
        $brands     = $pdo->query("SELECT * FROM THUONGHIEU")->fetchAll();
        $sports     = $pdo->query("SELECT * FROM THETHAO")->fetchAll();

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-themsanpham.php';
        include './views/quantri/quantri-chantrang.php';
    }

    public function suaSanPham()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Sửa sản phẩm - VAB Admin';
        $trangHienTai = 'admin-product-edit';
        $nhungTuController = true;

        $pdo = getConnection();
        $id  = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = (int)($_POST['id'] ?? 0);
            $name        = $_POST['name'] ?? '';
            $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            $category    = $_POST['category'] ?? '';
            $brand       = $_POST['brand'] ?? '';
            $price       = (int)($_POST['price'] ?? 0);
            $sale_price  = !empty($_POST['sale_price']) ? (int)$_POST['sale_price'] : null;
            $gender      = $_POST['gender'] ?? '';
            $sport       = $_POST['sport'] ?? '';
            $status      = (int)($_POST['status'] ?? 1);
            $description = $_POST['description'] ?? '';

            $inputSizes      = $_POST['sizes']     ?? [];
            $inputQuantities = $_POST['quantities'] ?? [];
            $sizeData   = [];
            $sizesCache = [];
            if (is_array($inputSizes) && is_array($inputQuantities)) {
                for ($i = 0; $i < count($inputSizes); $i++) {
                    $sz = trim($inputSizes[$i]);
                    if ($sz !== '') {
                        $qty = max(0, (int)($inputQuantities[$i] ?? 0));
                        $sizeData[$sz] = $qty;
                        $sizesCache[]  = $sz;
                    }
                }
            }
            $sizesStr = implode(',', $sizesCache);

            $image = $_POST['existing_image'] ?? '';
            if (!empty($_FILES['image']['name'])) {
                $target_dir = 'assets/images/products/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $image = time() . '_' . str_replace(',', '_', basename($_FILES['image']['name']));
                move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
            }

            $images_str = $_POST['existing_images'] ?? '';
            if (!empty($_FILES['sub_images']['name'][0])) {
                $target_dir    = 'assets/images/products/';
                if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
                $sub_images_list = [];
                $file_count = count($_FILES['sub_images']['name']);
                $limit = min($file_count, 3);
                for ($i = 0; $i < $limit; $i++) {
                    if ($_FILES['sub_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $sub_img_name = time() . '_sub_' . $i . '_' . str_replace(',', '_', basename($_FILES['sub_images']['name'][$i]));
                        if (move_uploaded_file($_FILES['sub_images']['tmp_name'][$i], $target_dir . $sub_img_name)) {
                            $sub_images_list[] = $sub_img_name;
                        }
                    }
                }
                if (!empty($sub_images_list)) {
                    $images_str = implode(',', $sub_images_list);
                }
            }

            $stmt = $pdo->prepare("UPDATE SANPHAM SET TenSP=?, DuongDan=?, MaDanhMuc=?, MaThuongHieu=?, Gia=?, GiaKhuyenMai=?, GioiTinh=?, TheThao=?, TrangThai=?, KichCo=?, AnhChinh=?, AnhPhu=?, MoTaSP=? WHERE MaSP=?");
            $stmt->execute([$name, $slug, $category, $brand, $price, $sale_price, $gender, $sport, $status, $sizesStr, $image, $images_str, $description, $id]);

            $stmtDel = $pdo->prepare("DELETE FROM QUANLYSP WHERE MaSP = ?");
            $stmtDel->execute([$id]);

            $stmtSize = $pdo->prepare("INSERT INTO QUANLYSP (MaSP, KichCo, SoLuong) VALUES (?, ?, ?)");
            foreach ($sizeData as $sz => $qty) {
                $stmtSize->execute([$id, $sz, $qty]);
            }

            header('Location: index.php?page=admin-products&msg=updated');
            exit;
        }

        if (!$id) {
            header('Location: index.php?page=admin-products');
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM SANPHAM WHERE MaSP = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if (!$product) {
            header('Location: index.php?page=admin-products');
            exit;
        }

        $categories = $pdo->query("SELECT * FROM DANHMUCSP")->fetchAll();
        $brands     = $pdo->query("SELECT * FROM THUONGHIEU")->fetchAll();
        $sports     = $pdo->query("SELECT * FROM THETHAO")->fetchAll();

        $stmtSizes = $pdo->prepare("SELECT * FROM QUANLYSP WHERE MaSP = ? ORDER BY KichCo ASC");
        $stmtSizes->execute([$id]);
        $productSizes = $stmtSizes->fetchAll();

        if (!class_exists('ProductModel')) {
            require './models/ProductModel.php';
        }
        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-suasanpham.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ EMPLOYEES ============
    public function nhanVien()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý nhân viên - VAB Admin';
        $trangHienTai = 'admin-employees';
        $nhungTuController = true;

        $pdo = getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
            $name     = $_POST['name']     ?? '';
            $email    = $_POST['email']    ?? '';
            $phone    = $_POST['phone']    ?? '';
            $role     = $_POST['role']     ?? 'staff';
            $password = password_hash($_POST['password'] ?? '123456', PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO NHANVIEN (HoTenNV, email, MatKhauNV, SDT, VaiTro) VALUES (?,?,?,?,?)");
            $stmt->execute([$name, $email, $password, $phone, $role]);
            header('Location: index.php?page=admin-employees&msg=added');
            exit;
        }

        if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
            $stmt = $pdo->prepare("UPDATE NHANVIEN SET TrangThai = IF(TrangThai='active','locked','active') WHERE id=?");
            $stmt->execute([$_GET['toggle']]);
            header('Location: index.php?page=admin-employees&msg=toggled');
            exit;
        }

        $stmt      = $pdo->query("SELECT * FROM NHANVIEN ORDER BY NgayLapTK DESC");
        $employees = $stmt->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-nhanvien.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ REVIEWS ============
    public function danhGia()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý đánh giá - VAB Admin';
        $trangHienTai = 'admin-reviews';
        $nhungTuController = true;

        $pdo = getConnection();

        if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
            $stmt = $pdo->prepare("UPDATE DANHGIASP SET TrangThai='approved' WHERE id=?");
            $stmt->execute([$_GET['approve']]);
            header('Location: index.php?page=admin-reviews&msg=approved');
            exit;
        }

        if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
            $stmt = $pdo->prepare("UPDATE DANHGIASP SET TrangThai='rejected' WHERE id=?");
            $stmt->execute([$_GET['reject']]);
            header('Location: index.php?page=admin-reviews&msg=rejected');
            exit;
        }

        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $stmt = $pdo->prepare("DELETE FROM DANHGIASP WHERE id=?");
            $stmt->execute([$_GET['delete']]);
            header('Location: index.php?page=admin-reviews&msg=deleted');
            exit;
        }

        $stmt = $pdo->query("
            SELECT dg.*, sp.TenSP AS TenSP,
                   COALESCE(kh.HoTenKH, dg.TenKH) AS HoTenKH
            FROM DANHGIASP dg
            LEFT JOIN SANPHAM sp ON dg.MaSP = sp.MaSP
            LEFT JOIN KHACHHANG kh ON dg.MaKH = kh.MaKH
            ORDER BY dg.NgayDG DESC
        ");
        $reviews = $stmt->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-danhgia.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ USERS (Khach hang) ============
    public function nguoiDung()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý khách hàng - VAB Admin';
        $trangHienTai = 'admin-users';
        $nhungTuController = true;

        $pdo = getConnection();

        if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
            $stmt = $pdo->prepare("UPDATE KHACHHANG SET TrangThai = IF(TrangThai='active','locked','active') WHERE MaKH=?");
            $stmt->execute([$_GET['toggle']]);
            header('Location: index.php?page=admin-users&msg=toggled');
            exit;
        }

        $users = $pdo->query("SELECT * FROM KHACHHANG ORDER BY NgayTao DESC")->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-nguoidung.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ VOUCHERS ============
    public function maGiamGia()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý mã giảm giá - VAB Admin';
        $trangHienTai = 'admin-vouchers';
        $nhungTuController = true;

        $pdo = getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_voucher'])) {
            $code      = strtoupper(trim($_POST['code'] ?? ''));
            $name      = $_POST['name']       ?? '';
            $type      = $_POST['type']       ?? 'percent';
            $value     = (int)($_POST['value']     ?? 0);
            $min_order = (int)($_POST['min_order']  ?? 0);
            $max_use   = (int)($_POST['max_use']    ?? 100);
            $start_date = $_POST['start_date'] ?? date('Y-m-d');
            $end_date   = $_POST['end_date']   ?? date('Y-m-d', strtotime('+30 days'));

            $stmt = $pdo->prepare("INSERT INTO MAGIAMGIA (MaGiamGia, Ten, Loai, GiaTri, DonHangToiThieu, SoLuot, NgayBD, NgayKT) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$code, $name, $type, $value, $min_order, $max_use, $start_date, $end_date]);
            header('Location: index.php?page=admin-vouchers&msg=added');
            exit;
        }

        if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
            $stmt = $pdo->prepare("DELETE FROM MAGIAMGIA WHERE id=?");
            $stmt->execute([$_GET['delete']]);
            header('Location: index.php?page=admin-vouchers&msg=deleted');
            exit;
        }

        $vouchers = $pdo->query("SELECT * FROM MAGIAMGIA ORDER BY id DESC")->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-magiamgia.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ ORDERS ============
    public function donHang()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý đơn hàng - VAB Admin';
        $trangHienTai = 'admin-orders';
        $nhungTuController = true;

        $pdo = getConnection();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
            $order_id   = (int)($_POST['order_id'] ?? 0);
            $new_status = $_POST['status'] ?? '';

            // Lấy trạng thái hiện tại của đơn hàng
            $stmtStatus = $pdo->prepare("SELECT TrangThaiDH FROM DONHANG WHERE MaDonHang = ?");
            $stmtStatus->execute([$order_id]);
            $current_status = $stmtStatus->fetchColumn();

            $allowedTransitions = [
                'pending' => ['confirmed', 'shipping', 'completed', 'cancelled'],
                'confirmed' => ['shipping', 'completed', 'cancelled'],
                'shipping' => ['completed', 'cancelled'],
                'delivered' => ['completed', 'cancelled'],
                'completed' => [],
                'cancelled' => []
            ];

            $allowed = $allowedTransitions[$current_status] ?? [];
            if (in_array($new_status, $allowed)) {
                $stmt = $pdo->prepare("UPDATE DONHANG SET TrangThaiDH = ? WHERE MaDonHang = ?");
                $stmt->execute([$new_status, $order_id]);

                // Gửi email nếu trạng thái chuyển sang 'confirmed' và phương thức thanh toán là 'sepay'
                if ($new_status === 'confirmed') {
                    $stmtPM = $pdo->prepare("SELECT PhuongThucTT FROM DONHANG WHERE MaDonHang = ?");
                    $stmtPM->execute([$order_id]);
                    $pm = strtolower($stmtPM->fetchColumn());

                    if ($pm === 'sepay') {
                        if (!function_exists('sendPaymentSuccessEmail')) {
                            require_once './helpers/mail_helper.php';
                        }
                        sendPaymentSuccessEmail($order_id);
                    }
                }

                header('Location: index.php?page=admin-orders&msg=updated');
                exit;
            } else {
                header('Location: index.php?page=admin-orders&msg=error_invalid_transition');
                exit;
            }
        }

        $orders = $pdo->query("SELECT dh.*, kh.HoTenKH AS customer_name FROM DONHANG dh LEFT JOIN KHACHHANG kh ON dh.MaKH = kh.MaKH ORDER BY dh.NgayTao DESC")->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-donhang.php';
        include './views/quantri/quantri-chantrang.php';
    }

    public function chiTietDonHang()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Chi tiết đơn hàng - VAB Admin';
        $trangHienTai = 'admin-orders';
        $nhungTuController = true;

        $pdo = getConnection();
        $id  = (int)($_GET['id'] ?? 0);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
            $new_status     = $_POST['status'] ?? '';

            // Lấy trạng thái hiện tại của đơn hàng
            $stmtStatus = $pdo->prepare("SELECT TrangThaiDH FROM DONHANG WHERE MaDonHang = ?");
            $stmtStatus->execute([$id]);
            $current_status = $stmtStatus->fetchColumn();

            $allowedTransitions = [
                'pending' => ['confirmed', 'shipping', 'completed', 'cancelled'],
                'confirmed' => ['shipping', 'completed', 'cancelled'],
                'shipping' => ['completed', 'cancelled'],
                'delivered' => ['completed', 'cancelled'],
                'completed' => [],
                'cancelled' => []
            ];

            $allowed = $allowedTransitions[$current_status] ?? [];
            if (in_array($new_status, $allowed)) {
                $stmt = $pdo->prepare("UPDATE DONHANG SET TrangThaiDH = ? WHERE MaDonHang = ?");
                $stmt->execute([$new_status, $id]);

                // Gửi email nếu trạng thái chuyển sang 'confirmed' và phương thức thanh toán là 'sepay'
                if ($new_status === 'confirmed') {
                    $stmtPM = $pdo->prepare("SELECT PhuongThucTT FROM DONHANG WHERE MaDonHang = ?");
                    $stmtPM->execute([$id]);
                    $pm = strtolower($stmtPM->fetchColumn());

                    if ($pm === 'sepay') {
                        if (!function_exists('sendPaymentSuccessEmail')) {
                            require_once './helpers/mail_helper.php';
                        }
                        sendPaymentSuccessEmail($id);
                    }
                }

                header('Location: index.php?page=admin-order-detail&id=' . $id . '&msg=updated');
                exit;
            } else {
                header('Location: index.php?page=admin-order-detail&id=' . $id . '&msg=error_invalid_transition');
                exit;
            }
        }

        $stmt = $pdo->prepare("SELECT dh.*, kh.HoTenKH AS customer_name FROM DONHANG dh LEFT JOIN KHACHHANG kh ON dh.MaKH = kh.MaKH WHERE dh.MaDonHang = ?");
        $stmt->execute([$id]);
        $order = $stmt->fetch();

        if (!$order) {
            header('Location: index.php?page=admin-orders');
            exit;
        }

        $stmt = $pdo->prepare("SELECT ct.*, sp.AnhChinh as AnhChinh FROM CHITIETDONHANG ct LEFT JOIN SANPHAM sp ON ct.MaSP = sp.MaSP WHERE ct.MaDonHang = ?");
        $stmt->execute([$id]);
        $items = $stmt->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-chitietdonhang.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ REVENUE ============
    public function doanhThu()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý doanh thu - VAB Admin';
        $trangHienTai = 'admin-revenue';
        $nhungTuController = true;

        $pdo = getConnection();

        $stmt = $pdo->query("
            SELECT DATE(NgayTao) AS day, COUNT(*) AS order_count, COALESCE(SUM(TongTien),0) AS revenue
            FROM DONHANG WHERE TrangThaiDH='completed'
            GROUP BY DATE(NgayTao) ORDER BY day DESC LIMIT 30
        ");
        $dailyRevenue = $stmt->fetchAll();

        $stmt = $pdo->query("
            SELECT DATE_FORMAT(NgayTao,'%Y-%m') AS month, COUNT(*) AS order_count, COALESCE(SUM(TongTien),0) AS revenue
            FROM DONHANG WHERE TrangThaiDH='completed'
            GROUP BY DATE_FORMAT(NgayTao,'%Y-%m') ORDER BY month DESC LIMIT 12
        ");
        $monthlyRevenue = $stmt->fetchAll();

        $stmt = $pdo->query("
            SELECT YEAR(NgayTao) AS year, COUNT(*) AS order_count, COALESCE(SUM(TongTien),0) AS revenue
            FROM DONHANG WHERE TrangThaiDH='completed'
            GROUP BY YEAR(NgayTao) ORDER BY year DESC LIMIT 5
        ");
        $yearlyRevenue = $stmt->fetchAll();

        $stmt = $pdo->query("
            SELECT ct.TenSP AS TenSP, SUM(ct.SoLuongSP) AS total_sold, SUM(ct.SoLuongSP * ct.DonGia) AS total_revenue
            FROM CHITIETDONHANG ct
            JOIN DONHANG dh ON ct.MaDonHang = dh.MaDonHang
            WHERE dh.TrangThaiDH = 'completed'
            GROUP BY ct.TenSP ORDER BY total_sold DESC LIMIT 10
        ");
        $bestSellers = $stmt->fetchAll();

        $stmt = $pdo->query("
            SELECT ct.TenSP AS TenSP, SUM(ct.SoLuongSP) AS total_sold, SUM(ct.SoLuongSP * ct.DonGia) AS total_revenue
            FROM CHITIETDONHANG ct
            JOIN DONHANG dh ON ct.MaDonHang = dh.MaDonHang
            WHERE dh.TrangThaiDH = 'completed'
            GROUP BY ct.TenSP ORDER BY total_sold ASC LIMIT 10
        ");
        $worstSellers = $stmt->fetchAll();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-doanhthu.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ SETTINGS ============
    public function cauhinh()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Cài đặt - VAB Admin';
        $trangHienTai = 'admin-settings';
        $nhungTuController = true;

        $pdo        = getConnection();
        $employeeId = $_SESSION['ma_nhanvien'];
        $error  = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update_info'])) {
                $name  = trim($_POST['name']  ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');

                if (empty($name) || empty($email)) {
                    $error = 'Họ tên và Email không được để trống!';
                } else {
                    try {
                        $stmt = $pdo->prepare("SELECT id FROM NHANVIEN WHERE email = ? AND id != ?");
                        $stmt->execute([$email, $employeeId]);
                        if ($stmt->fetch()) {
                            $error = 'Email này đã được sử dụng bởi một tài khoản khác!';
                        } else {
                            $stmt = $pdo->prepare("UPDATE NHANVIEN SET HoTenNV = ?, email = ?, SDT = ? WHERE id = ?");
                            $stmt->execute([$name, $email, $phone, $employeeId]);
                            $_SESSION['ten_nhanvien'] = $name;
                            $success = 'Cập nhật thông tin tài khoản thành công!';
                        }
                    } catch (PDOException $e) {
                        $error = 'Lỗi hệ thống: ' . $e->getMessage();
                    }
                }
            } elseif (isset($_POST['change_password'])) {
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword     = $_POST['new_password']     ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';

                if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                    $error = 'Vui lòng điền đầy đủ tất cả các trường mật khẩu!';
                } elseif ($newPassword !== $confirmPassword) {
                    $error = 'Mật khẩu mới và xác nhận mật khẩu không khớp!';
                } elseif (strlen($newPassword) < 6) {
                    $error = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
                } else {
                    try {
                        $stmt = $pdo->prepare("SELECT MatKhauNV FROM NHANVIEN WHERE id = ?");
                        $stmt->execute([$employeeId]);
                        $emp = $stmt->fetch();
                        if ($emp && password_verify($currentPassword, $emp['MatKhauNV'])) {
                            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                            $stmt = $pdo->prepare("UPDATE NHANVIEN SET MatKhauNV = ? WHERE id = ?");
                            $stmt->execute([$newHash, $employeeId]);
                            $success = 'Đổi mật khẩu thành công!';
                        } else {
                            $error = 'Mật khẩu hiện tại không chính xác!';
                        }
                    } catch (PDOException $e) {
                        $error = 'Lỗi hệ thống: ' . $e->getMessage();
                    }
                }
            }
        }

        $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE id = ?");
        $stmt->execute([$employeeId]);
        $employee = $stmt->fetch();

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-cauhinh.php';
        include './views/quantri/quantri-chantrang.php';
    }

    // ============ LOGIN ============
    public function dangNhap()
    {
        $tieuDeTrang   = 'Admin Login - VAB';
        $trangHienTai = 'admin-login';
        $nhungTuController = true;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = $_POST['email']    ?? '';
            $password = $_POST['password'] ?? '';

            $pdo  = getConnection();
            $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE email = ?");
            $stmt->execute([$email]);
            $employee = $stmt->fetch();

            if ($employee && password_verify($password, $employee['MatKhauNV'])) {
                if ($employee['TrangThai'] === 'locked') {
                    $error = 'Tài khoản của bạn đã bị khóa!';
                } else {
                    $_SESSION['ma_nhanvien']   = $employee['id'];
                    $_SESSION['ten_nhanvien'] = $employee['HoTenNV'];
                    $_SESSION['vaitro_nhanvien'] = $employee['VaiTro'];
                    header('Location: index.php?page=admin-dashboard');
                    exit;
                }
            } else {
                $error = 'Email hoặc mật khẩu không đúng!';
            }
        }

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-dangnhap.php';
        include './views/quantri/quantri-chantrang.php';
    }

    public function dangXuat()
    {
        session_destroy();
        header('Location: index.php?page=admin-login');
        exit;
    }

    // ============ CATEGORIES (Danh muc) ============
    public function danhMuc()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Quản lý danh mục - VAB Admin';
        $trangHienTai = 'admin-categories';
        $nhungTuController = true;

        $pdo = getConnection();
        $error = '';
        $success = '';

        $tab = $_GET['tab'] ?? 'category';
        if (!in_array($tab, ['category', 'brand', 'sport'])) {
            $tab = 'category';
        }

        // Xử lý thêm mới danh mục / thương hiệu / thể thao
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_category'])) {
                $name = trim($_POST['name'] ?? '');
                $status = (int)($_POST['status'] ?? 1);

                if (!empty($name)) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeVietnameseAccents($name))));
                    try {
                        $stmt = $pdo->prepare("INSERT INTO DANHMUCSP (TenDM, DuongDan, TrangThai) VALUES (?, ?, ?)");
                        $stmt->execute([$name, $slug, $status]);
                        header('Location: index.php?page=admin-categories&tab=category&msg=added');
                        exit;
                    } catch (PDOException $e) {
                        $error = 'Lỗi hệ thống hoặc Tên danh mục đã tồn tại!';
                    }
                } else {
                    $error = 'Vui lòng nhập tên danh mục!';
                }
            }

            if (isset($_POST['add_brand'])) {
                $name = trim($_POST['name'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $status = (int)($_POST['status'] ?? 1);

                if (!empty($name)) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeVietnameseAccents($name))));
                    try {
                        $stmt = $pdo->prepare("INSERT INTO THUONGHIEU (TenThuongHieu, DuongDan, MoTa, TrangThai) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$name, $slug, $description, $status]);
                        header('Location: index.php?page=admin-categories&tab=brand&msg=added');
                        exit;
                    } catch (PDOException $e) {
                        $error = 'Lỗi hệ thống hoặc Tên thương hiệu đã tồn tại!';
                    }
                } else {
                    $error = 'Vui lòng nhập tên thương hiệu!';
                }
            }

            if (isset($_POST['add_sport'])) {
                $name = trim($_POST['name'] ?? '');
                $status = (int)($_POST['status'] ?? 1);

                if (!empty($name)) {
                    $slug = 'sport-' . strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeVietnameseAccents($name))));
                    try {
                        $stmt = $pdo->prepare("INSERT INTO THETHAO (TenSport, DuongDan, TrangThai) VALUES (?, ?, ?)");
                        $stmt->execute([$name, $slug, $status]);
                        header('Location: index.php?page=admin-categories&tab=sport&msg=added');
                        exit;
                    } catch (PDOException $e) {
                        $error = 'Lỗi hệ thống hoặc Tên bộ môn thể thao đã tồn tại!';
                    }
                } else {
                    $error = 'Vui lòng nhập tên bộ môn thể thao!';
                }
            }
        }

        // Xử lý bật tắt trạng thái nhanh
        if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
            $id = (int)$_GET['toggle_status'];
            if ($tab === 'category') {
                $stmt = $pdo->prepare("UPDATE DANHMUCSP SET TrangThai = IF(TrangThai=1, 0, 1) WHERE MaDanhMuc = ?");
                $stmt->execute([$id]);
            } elseif ($tab === 'brand') {
                $stmt = $pdo->prepare("UPDATE THUONGHIEU SET TrangThai = IF(TrangThai=1, 0, 1) WHERE MaThuongHieu = ?");
                $stmt->execute([$id]);
            } elseif ($tab === 'sport') {
                $stmt = $pdo->prepare("UPDATE THETHAO SET TrangThai = IF(TrangThai=1, 0, 1) WHERE id = ?");
                $stmt->execute([$id]);
            }
            header('Location: index.php?page=admin-categories&tab=' . $tab . '&msg=updated');
            exit;
        }

        $categories = $pdo->query("SELECT * FROM DANHMUCSP ORDER BY MaDanhMuc DESC")->fetchAll();
        $brands = $pdo->query("SELECT * FROM THUONGHIEU ORDER BY MaThuongHieu DESC")->fetchAll();
        $sports = $pdo->query("SELECT * FROM THETHAO ORDER BY id DESC")->fetchAll();
        $msg = $_GET['msg'] ?? '';

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-danhmuc.php';
        include './views/quantri/quantri-chantrang.php';
    }

    public function suaDanhMuc()
    {
        $this->checkAuth();
        $tieuDeTrang   = 'Sửa danh mục - VAB Admin';
        $trangHienTai = 'admin-categories';
        $nhungTuController = true;

        $pdo = getConnection();
        $id  = (int)($_GET['id'] ?? 0);
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'] ?? 0;
            $name = trim($_POST['name'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $status = (int)($_POST['status'] ?? 1);

            if (empty($slug)) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeVietnameseAccents($name))));
            } else {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $this->removeVietnameseAccents($slug))));
            }

            if (!empty($name)) {
                try {
                    $stmt = $pdo->prepare("UPDATE DANHMUCSP SET TenDM = ?, DuongDan = ?, TrangThai = ? WHERE MaDanhMuc = ?");
                    $stmt->execute([$name, $slug, $status, $id]);
                    header('Location: index.php?page=admin-categories&msg=updated');
                    exit;
                } catch (PDOException $e) {
                    $error = 'Lỗi hệ thống hoặc Tên/Đường dẫn danh mục đã trùng lặp!';
                }
            } else {
                $error = 'Vui lòng nhập tên danh mục!';
            }
        }

        if (!$id) {
            header('Location: index.php?page=admin-categories');
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM DANHMUCSP WHERE MaDanhMuc = ?");
        $stmt->execute([$id]);
        $category = $stmt->fetch();

        if (!$category) {
            header('Location: index.php?page=admin-categories');
            exit;
        }

        include './views/quantri/quantri-dautrang.php';
        include './views/quantri/quantri-suadanhmuc.php';
        include './views/quantri/quantri-chantrang.php';
    }

    public function xoaDanhMuc()
    {
        $this->checkAuth();
        $pdo = getConnection();
        $id = (int)($_GET['id'] ?? 0);
        $tab = $_GET['tab'] ?? 'category';

        if ($id > 0) {
            if ($tab === 'category') {
                // Kiểm tra xem danh mục có sản phẩm nào không
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM SANPHAM WHERE MaDanhMuc = ?");
                $stmtCheck->execute([$id]);
                $hasProducts = (int)$stmtCheck->fetchColumn() > 0;

                if ($hasProducts) {
                    header('Location: index.php?page=admin-categories&tab=category&msg=error_has_products');
                    exit;
                } else {
                    $stmt = $pdo->prepare("DELETE FROM DANHMUCSP WHERE MaDanhMuc = ?");
                    $stmt->execute([$id]);
                    header('Location: index.php?page=admin-categories&tab=category&msg=deleted');
                    exit;
                }
            } elseif ($tab === 'brand') {
                // Kiểm tra xem thương hiệu có sản phẩm nào không
                $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM SANPHAM WHERE MaThuongHieu = ?");
                $stmtCheck->execute([$id]);
                $hasProducts = (int)$stmtCheck->fetchColumn() > 0;

                if ($hasProducts) {
                    header('Location: index.php?page=admin-categories&tab=brand&msg=error_has_products');
                    exit;
                } else {
                    $stmt = $pdo->prepare("DELETE FROM THUONGHIEU WHERE MaThuongHieu = ?");
                    $stmt->execute([$id]);
                    header('Location: index.php?page=admin-categories&tab=brand&msg=deleted');
                    exit;
                }
            } elseif ($tab === 'sport') {
                // Lấy tên bộ môn thể thao để kiểm tra trong sản phẩm
                $stmtGet = $pdo->prepare("SELECT TenSport FROM THETHAO WHERE id = ?");
                $stmtGet->execute([$id]);
                $sportName = $stmtGet->fetchColumn();

                if ($sportName) {
                    // Kiểm tra xem bộ môn có sản phẩm nào không
                    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM SANPHAM WHERE TheThao = ?");
                    $stmtCheck->execute([$sportName]);
                    $hasProducts = (int)$stmtCheck->fetchColumn() > 0;

                    if ($hasProducts) {
                        header('Location: index.php?page=admin-categories&tab=sport&msg=error_has_products');
                        exit;
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM THETHAO WHERE id = ?");
                        $stmt->execute([$id]);
                        header('Location: index.php?page=admin-categories&tab=sport&msg=deleted');
                        exit;
                    }
                }
            }
        }

        header('Location: index.php?page=admin-categories&tab=' . $tab);
        exit;
    }

    /** Helper chuyển đổi tiếng Việt không dấu */
    private function removeVietnameseAccents($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", "a", $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", "e", $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", "i", $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", "o", $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", "u", $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", "y", $str);
        $str = preg_replace("/(đ)/", "d", $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", "A", $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", "E", $str);
        $str = preg_replace("/(Ì|Í|Ị|R|Ĩ)/", "I", $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", "O", $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", "U", $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", "Y", $str);
        $str = preg_replace("/(Đ)/", "D", $str);
        return $str;
    }
}
