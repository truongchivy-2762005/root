<?php
/**
 * OrderController - Handles user order list and detail viewing
 * Quản lý danh sách đơn hàng và chi tiết đơn hàng của người dùng
 */
class OrderController {

    /**
     * Kiểm tra người dùng đã đăng nhập chưa
     */
    private function checkLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['ma_nguoidung'])) {
            header('Location: index.php?page=login');
            exit;
        }
    }

    /**
     * Hiển thị danh sách đơn hàng
     */
    public function danhSach() {
        $this->checkLogin();

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $donHangModel = new OrderModel();
        $maKH = $_SESSION['ma_nguoidung'];

        $orders = $donHangModel->getOrdersByUserId($maKH);

        $tieuDeTrang = 'Đơn hàng của tôi - VAB';
        $trangHienTai = 'orders';
        $nhungTuController = true;

        include './views/giaodien/dautrang.php';
        include './views/giaodien/donhang.php';
        include './views/giaodien/chantrang.php';
    }

    /**
     * Hiển thị chi tiết một đơn hàng
     */
    public function chiTiet() {
        $this->checkLogin();

        $maDonHang = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($maDonHang <= 0) {
            header('Location: index.php?page=orders');
            exit;
        }

        if (!class_exists('OrderModel')) {
            require './models/OrderModel.php';
        }
        $donHangModel = new OrderModel();
        $maKH = $_SESSION['ma_nguoidung'];

        // Bảo mật: Người dùng chỉ xem được đơn hàng của chính họ
        $order = $donHangModel->getOrderByIdAndUserId($maDonHang, $maKH);

        if (!$order) {
            // Đơn hàng không tồn tại hoặc không thuộc về người dùng đang đăng nhập
            header('Location: index.php?page=orders');
            exit;
        }

        $orderItems = $donHangModel->getOrderItems($maDonHang);

        $tieuDeTrang = 'Chi tiết đơn hàng #' . str_pad($maDonHang, 5, '0', STR_PAD_LEFT) . ' - VAB';
        $trangHienTai = 'orders';
        $nhungTuController = true;

        include './views/giaodien/dautrang.php';
        include './views/giaodien/chitiet-donhang.php';
        include './views/giaodien/chantrang.php';
    }

    /**
     * Helper chuyển đổi trạng thái đơn hàng sang Badge CSS class và nhãn tiếng Việt
     */
    public static function getStatusInfo($status) {
        $statusClean = strtolower(trim($status));
        switch ($statusClean) {
            case 'pending':
            case 'chờ xác nhận':
            case 'cho xac nhan':
                return [
                    'class' => 'status-pending',
                    'label' => 'Chờ xác nhận'
                ];
            case 'confirmed':
            case 'đã xác nhận':
            case 'da xac nhan':
                return [
                    'class' => 'status-confirmed',
                    'label' => 'Đã thanh toán'
                ];
            case 'shipping':
            case 'đang giao hàng':
            case 'dang giao hang':
                return [
                    'class' => 'status-shipping',
                    'label' => 'Đang giao hàng'
                ];
            case 'delivered':
            case 'completed':
            case 'đã giao hàng':
            case 'da giao hang':
                return [
                    'class' => 'status-delivered',
                    'label' => 'Đã giao hàng'
                ];
            case 'cancelled':
            case 'đã hủy':
            case 'da huy':
                return [
                    'class' => 'status-cancelled',
                    'label' => 'Đã hủy'
                ];
            default:
                return [
                    'class' => 'status-pending',
                    'label' => $status
                ];
        }
    }
}
