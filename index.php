<?php
/**
 * VAB - FRONT CONTROLLER (Simple Include, No define/require_once)
 * Đầu vào duy nhất. Phân tích URL (?controller=xxx&action=yyy)
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Include database config (dùng require, không define)
require './models/database.php';

// Lấy controller & action từ URL
$controllerName = isset($_GET['controller'])
    ? ucfirst(strtolower(trim($_GET['controller']))) . 'Controller'
    : 'HomeController';
$actionName = isset($_GET['action'])
    ? strtolower(trim($_GET['action']))
    : 'trangChu';

// Map alias cho link cũ (?page=xxx)
$page = isset($_GET['page']) ? trim($_GET['page']) : '';
if ($page) {
    $pageMap = [
        'home'             => ['controller' => 'Home',    'action' => 'trangChu'],
        'products'         => ['controller' => 'Product', 'action' => 'danhSach'],
        'search'           => ['controller' => 'Product', 'action' => 'timKiem'],
        'product-detail'   => ['controller' => 'Product', 'action' => 'chiTiet'],
        'load-more'        => ['controller' => 'Product', 'action' => 'taiThem'],
        'add-to-cart'      => ['controller' => 'Product', 'action' => 'themGioHang'],
        'men'              => ['controller' => 'Product', 'action' => 'nam'],
        'women'            => ['controller' => 'Product', 'action' => 'nu'],
        'sale'             => ['controller' => 'Product', 'action' => 'giamGia'],
        'sport-running'    => ['controller' => 'Product', 'action' => 'theThaoChayBo'],
        'sport-gym'        => ['controller' => 'Product', 'action' => 'theThaoGym'],
        'sport-football'   => ['controller' => 'Product', 'action' => 'theThaoBongDa'],
        'sport-basketball' => ['controller' => 'Product', 'action' => 'theThaoBongRo'],
        'sport-tennis'     => ['controller' => 'Product', 'action' => 'theThaoTennis'],
        'brand-nike'       => ['controller' => 'Product', 'action' => 'thuongHieuNike'],
        'brand-adidas'     => ['controller' => 'Product', 'action' => 'thuongHieuAdidas'],
        'brand-puma'       => ['controller' => 'Product', 'action' => 'thuongHieuPuma'],
        'cart'             => ['controller' => 'Product', 'action' => 'gioHang'],
        'cart-update'      => ['controller' => 'Product', 'action' => 'capNhatGioHang'],
        'cart-remove'      => ['controller' => 'Product', 'action' => 'xoaGioHang'],
        'checkout'         => ['controller' => 'Product', 'action' => 'thanhToan'],
        'apply-coupon'     => ['controller' => 'Product', 'action' => 'apDungMaGiamGia'],
        'remove-coupon'    => ['controller' => 'Product', 'action' => 'xoaMaGiamGia'],
        'login'            => ['controller' => 'Auth',    'action' => 'dangNhap'],
        'register'         => ['controller' => 'Auth',    'action' => 'dangKy'],
        'logout'           => ['controller' => 'Auth',    'action' => 'dangXuat'],
        'orders'           => ['controller' => 'Order',   'action' => 'danhSach'],
        'order-detail'     => ['controller' => 'Order',   'action' => 'chiTiet'],
        'don-hang'         => ['controller' => 'Order',   'action' => 'danhSach'],
        'chi-tiet-don-hang'=> ['controller' => 'Order',   'action' => 'chiTiet'],
        // Admin pages
        'admin'             => ['controller' => 'Admin',   'action' => 'trangChu'],
        'admin-dashboard'   => ['controller' => 'Admin',   'action' => 'trangChu'],
        'admin-products'    => ['controller' => 'Admin',   'action' => 'sanPham'],
        'admin-product-add' => ['controller' => 'Admin',   'action' => 'themSanPham'],
        'admin-product-edit'=> ['controller' => 'Admin',   'action' => 'suaSanPham'],
        'admin-orders'      => ['controller' => 'Admin',   'action' => 'donHang'],
        'admin-order-detail'=> ['controller' => 'Admin',   'action' => 'chiTietDonHang'],
        'admin-categories'      => ['controller' => 'Admin',   'action' => 'danhMuc'],
        'admin-category-edit'   => ['controller' => 'Admin',   'action' => 'suaDanhMuc'],
        'admin-category-delete' => ['controller' => 'Admin',   'action' => 'xoaDanhMuc'],
        'admin-users'       => ['controller' => 'Admin',   'action' => 'nguoiDung'],
        'admin-employees'   => ['controller' => 'Admin',   'action' => 'nhanVien'],
        'admin-reviews'     => ['controller' => 'Review',  'action' => 'quantriDanhSach'],
        'admin-review-approve' => ['controller' => 'Review', 'action' => 'duyet'],
        'admin-review-reject' => ['controller' => 'Review', 'action' => 'tuChoi'],
        'admin-review-delete' => ['controller' => 'Review', 'action' => 'xoa'],
        'review-store'      => ['controller' => 'Review',  'action' => 'luu'],
        'admin-vouchers'    => ['controller' => 'Admin',   'action' => 'maGiamGia'],
        'admin-revenue'     => ['controller' => 'Admin',   'action' => 'doanhThu'],
        'admin-settings'    => ['controller' => 'Admin',   'action' => 'cauhinh'],
        'admin-login'       => ['controller' => 'Admin',   'action' => 'dangNhap'],
        'admin-logout'      => ['controller' => 'Admin',   'action' => 'dangXuat'],
        'admin-mail'        => ['controller' => 'Mail',    'action' => 'hopThu'],
        'admin-mail-create' => ['controller' => 'Mail',    'action' => 'vietThu'],
        'admin-mail-store'  => ['controller' => 'Mail',    'action' => 'guiThu'],
        'inbox'             => ['controller' => 'Account', 'action' => 'hopThu'],
        'google-login'      => ['controller' => 'Auth',    'action' => 'dangNhapGoogle'],
        'sepay-payment'      => ['controller' => 'Product', 'action' => 'thanhtoanSepay'],
        'sepay-check-status' => ['controller' => 'Product', 'action' => 'kiemtraTrangthaiSepay'],
        'sepay-webhook'      => ['controller' => 'SePayWebhook', 'action' => 'xuly'],
    ];
    if (isset($pageMap[$page])) {
        $controllerName = $pageMap[$page]['controller'] . 'Controller';
        $actionName     = $pageMap[$page]['action'];
    } else {
        if (strpos($page, 'brand-') === 0) {
            $controllerName = 'ProductController';
            $actionName     = 'chiTietThuongHieuDynamic';
            $_GET['brand_slug'] = substr($page, 6);
        } elseif (strpos($page, 'sport-') === 0) {
            $controllerName = 'ProductController';
            $actionName     = 'chiTietTheThaoDynamic';
            $_GET['sport_slug'] = $page;
        } else {
            header('Location: index.php?page=home');
            exit;
        }
    }
}

// Include controller file (dùng include, không require_once)
$controllerFile = './controllers/' . $controllerName . '.php';
if (!file_exists($controllerFile)) {
    header('Location: index.php?page=home');
    exit;
}
include $controllerFile;

// Kiểm tra class tồn tại
if (!class_exists($controllerName)) {
    header('Location: index.php?page=home');
    exit;
}

$controller = new $controllerName();

// Kiểm tra action tồn tại
if (!method_exists($controller, $actionName)) {
    header('Location: index.php?page=home');
    exit;
}

// Gọi action
$controller->$actionName();