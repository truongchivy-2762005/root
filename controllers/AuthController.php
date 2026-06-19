<?php
/**
 * AuthController - Handles login, register, logout
 * Include model trước, sau đó include header -> view content -> footer
 */
class AuthController {
    public function dangNhap() {
        $tieuDeTrang = 'Đăng nhập - VAB';
        $trangHienTai = 'login';
        $nhungTuController = true;
        if (!class_exists('UserModel')) {
            require './models/UserModel.php';
        }
        $userModel = new UserModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $matKhau = $_POST['password'] ?? '';
            $nguoiDung = $userModel->authenticate($email, $matKhau);
            
            if (!$nguoiDung) {
                // Nếu không có ở bảng users hoặc sai mật khẩu, kiểm tra xem có ở bảng employees không để đồng bộ sang users
                $pdo = getConnection();
                $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE email = ?");
                $stmt->execute([$email]);
                $nhanVien = $stmt->fetch();
                
                if ($nhanVien && password_verify($matKhau, $nhanVien['MatKhauNV'])) {
                    $nguoiDungDaCo = $userModel->getByEmail($email);
                    if (!$nguoiDungDaCo) {
                        // Chua co -> Tao moi
                        $stmtIns = $pdo->prepare("INSERT INTO KHACHHANG (HoTenKH, email, MatKhauKH, SDT, NgayTao) VALUES (?, ?, ?, ?, NOW())");
                        $stmtIns->execute([$nhanVien['HoTenNV'], $nhanVien['email'], $nhanVien['MatKhauNV'], $nhanVien['SDT'] ?? '']);
                    } else {
                        // Da co -> Cap nhat mat khau tu NHANVIEN sang KHACHHANG de dong bo
                        $stmtUpd = $pdo->prepare("UPDATE KHACHHANG SET HoTenKH = ?, MatKhauKH = ?, SDT = ? WHERE MaKH = ?");
                        $stmtUpd->execute([$nhanVien['HoTenNV'], $nhanVien['MatKhauNV'], $nhanVien['SDT'] ?? '', $nguoiDungDaCo['MaKH']]);
                    }
                    
                    // Xác thực lại
                    $nguoiDung = $userModel->authenticate($email, $matKhau);
                }
            }

            if ($nguoiDung) {
                if (($nguoiDung['TrangThai'] ?? 'active') === 'locked') {
                    $error = 'Tài khoản của bạn đã bị khóa!';
                } else {
                    $_SESSION['ma_nguoidung']    = $nguoiDung['MaKH'];
                    $_SESSION['ten_nguoidung']  = $nguoiDung['HoTenKH'];
                    $_SESSION['email_nguoidung'] = $nguoiDung['email'];
                    $_SESSION['vaitro_nguoidung']  = 'customer';

                    // Dong bo phien dang nhap Admin (kiem tra bang NHANVIEN)
                    $pdo  = getConnection();
                    $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE email = ?");
                    $stmt->execute([$nguoiDung['email']]);
                    $nhanVien = $stmt->fetch();
                    if ($nhanVien) {
                        $_SESSION['ma_nhanvien']   = $nhanVien['id'];
                        $_SESSION['ten_nhanvien'] = $nhanVien['HoTenNV'];
                        $_SESSION['vaitro_nhanvien'] = $nhanVien['VaiTro'];
                        $_SESSION['vaitro_nguoidung']     = $nhanVien['VaiTro'];
                    }

                    header('Location: index.php?page=home');
                    exit;
                }
            } else {
                $error = 'Email hoặc mật khẩu không đúng!';
            }
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/dangnhap.php';
        include './views/giaodien/chantrang.php';
    }

    public function dangKy() {
        $tieuDeTrang = 'Đăng ký - VAB';
        $trangHienTai = 'register';
        $nhungTuController = true;
        if (!class_exists('UserModel')) {
            require './models/UserModel.php';
        }
        $userModel = new UserModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            
            $nguoiDungDaCo = $userModel->getByEmail($email);
            if ($nguoiDungDaCo) {
                $error = 'Email này đã được đăng ký sử dụng!';
            } else {
                $userId = $userModel->create([
                    'HoTenKH'   => $name,
                    'email'    => $email,
                    'SDT'    => $phone,
                    'MatKhauKH' => $password,
                ]);
                
                if ($userId) {
                    $_SESSION['ma_nguoidung']    = $userId;
                    $_SESSION['ten_nguoidung']  = $name;
                    $_SESSION['email_nguoidung'] = $email;
                    $_SESSION['vaitro_nguoidung']  = 'customer';
                    header('Location: index.php?page=home');
                    exit;
                } else {
                    $error = 'Có lỗi xảy ra trong quá trình đăng ký!';
                }
            }
        }

        include './views/giaodien/dautrang.php';
        include './views/giaodien/dangky.php';
        include './views/giaodien/chantrang.php';
    }

    public function dangNhapGoogle() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Nhận ID Token từ Google
        $idToken = $_POST['credential'] ?? null;
        if (!$idToken) {
            header('Location: index.php?page=login');
            exit;
        }

        // Require UserModel ở đây
        if (!class_exists('UserModel')) {
            require './models/UserModel.php';
        }
        $userModel = new UserModel();

        // 2. Xác thực ID Token qua Google API endpoint
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($idToken);
        
        $response = null;
        if (function_exists('curl_version')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Tránh lỗi SSL trên localhost
            $response = curl_exec($ch);
            curl_close($ch);
        } else {
            $response = @file_get_contents($url);
        }

        if (!$response) {
            $tieuDeTrang = 'Lỗi đăng nhập - VAB';
            $trangHienTai = 'login';
            $error = 'Không thể kết nối đến máy chủ Google để xác thực!';
            include './views/giaodien/dautrang.php';
            include './views/giaodien/dangnhap.php';
            include './views/giaodien/chantrang.php';
            return;
        }

        $payload = json_decode($response, true);
        if (!isset($payload['email'])) {
            $tieuDeTrang = 'Lỗi đăng nhập - VAB';
            $trangHienTai = 'login';
            $error = 'Thông tin Google gửi về không hợp lệ hoặc thiếu Email!';
            include './views/giaodien/dautrang.php';
            include './views/giaodien/dangnhap.php';
            include './views/giaodien/chantrang.php';
            return;
        }

        $email = $payload['email'];
        $name = $payload['name'] ?? $email;

        // 3. Kiểm tra người dùng đã tồn tại trong hệ thống chưa
        $nguoiDung = $userModel->getByEmail($email);

        if ($nguoiDung) {
            // Kiem tra trang thai tai khoan co bi khoa khong
            if (($nguoiDung['TrangThai'] ?? 'active') === 'locked') {
                $tieuDeTrang = 'Tài khoản bị khóa - VAB';
                $trangHienTai = 'login';
                $error = 'Tài khoản của bạn đã bị khóa!';
                include './views/giaodien/dautrang.php';
                include './views/giaodien/dangnhap.php';
                include './views/giaodien/chantrang.php';
                return;
            }
        } else {
            // Nếu chưa tồn tại thì tự động tạo mới tài khoản
            $randomPassword = bin2hex(random_bytes(8));
            $userId = $userModel->create([
                'HoTenKH'   => $name,
                'email'    => $email,
                'MatKhauKH' => $randomPassword,
                'SDT'    => '',
            ]);

            $nguoiDung = $userModel->getById($userId);
        }

        // 4. Thiết lập session đăng nhập và chuyển hướng về trang chủ
        if ($nguoiDung) {
            $_SESSION['ma_nguoidung']    = $nguoiDung['MaKH'];
            $_SESSION['ten_nguoidung']  = $nguoiDung['HoTenKH'];
            $_SESSION['email_nguoidung'] = $nguoiDung['email'];
            $_SESSION['vaitro_nguoidung']  = 'customer';

            // Dong bo phien Admin (kiem tra bang NHANVIEN)
            $pdo  = getConnection();
            $stmt = $pdo->prepare("SELECT * FROM NHANVIEN WHERE email = ?");
            $stmt->execute([$nguoiDung['email']]);
            $nhanVien = $stmt->fetch();
            if ($nhanVien) {
                $_SESSION['ma_nhanvien']   = $nhanVien['id'];
                $_SESSION['ten_nhanvien'] = $nhanVien['HoTenNV'];
                $_SESSION['vaitro_nhanvien'] = $nhanVien['VaiTro'];
                $_SESSION['vaitro_nguoidung']     = $nhanVien['VaiTro'];
            }

            header('Location: index.php?page=home');
            exit;
        } else {
            $tieuDeTrang = 'Lỗi đăng nhập - VAB';
            $trangHienTai = 'login';
            $error = 'Không thể khởi tạo phiên đăng nhập từ tài khoản Google!';
            include './views/giaodien/dautrang.php';
            include './views/giaodien/dangnhap.php';
            include './views/giaodien/chantrang.php';
        }
    }

    public function dangXuat() {
        session_destroy();
        header('Location: index.php?page=home');
        exit;
    }
}