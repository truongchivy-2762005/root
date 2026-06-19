<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tieuDeTrang ?? 'VAB Admin' ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="assets/css/admin.css?v=<?= time() ?>">
</head>

<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <a href="index.php?page=admin-dashboard" class="sidebar-logo">
                    <img src="assets/images/logo/logo.png" alt="VAB Logo" style="height:60px; width:auto; filter:brightness(0) invert(1);">
                </a>
                <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
            </div>
            <nav class="sidebar-menu">
                <a href="index.php?page=admin-dashboard" class="menu-item <?= $trangHienTai == 'admin-dashboard' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-chart-pie"></i></span><span class="menu-text">Tổng quan</span>
                </a>
                <a href="index.php?page=admin-products" class="menu-item <?= strpos($trangHienTai, 'admin-product') !== false ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-box"></i></span><span class="menu-text">Sản phẩm</span>
                </a>
                <a href="index.php?page=admin-categories" class="menu-item <?= strpos($trangHienTai, 'admin-categor') !== false ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-folder"></i></span><span class="menu-text">Danh mục</span>
                </a>
                <a href="index.php?page=admin-employees" class="menu-item <?= $trangHienTai == 'admin-employees' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-user-tie"></i></span><span class="menu-text">Nhân viên</span>
                </a>
                <a href="index.php?page=admin-reviews" class="menu-item <?= $trangHienTai == 'admin-reviews' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-star"></i></span><span class="menu-text">Đánh giá</span>
                </a>
                <a href="index.php?page=admin-users" class="menu-item <?= $trangHienTai == 'admin-users' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-users"></i></span><span class="menu-text">Khách hàng</span>
                </a>
                <a href="index.php?page=admin-vouchers" class="menu-item <?= $trangHienTai == 'admin-vouchers' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-ticket-alt"></i></span><span class="menu-text">Mã giảm giá</span>
                </a>
                <a href="index.php?page=admin-orders" class="menu-item <?= strpos($trangHienTai, 'admin-order') !== false ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-shopping-cart"></i></span><span class="menu-text">Đơn hàng</span>
                </a>
                <a href="index.php?page=admin-revenue" class="menu-item <?= $trangHienTai == 'admin-revenue' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-chart-line"></i></span><span class="menu-text">Doanh thu</span>
                </a>
                <a href="index.php?page=admin-mail" class="menu-item <?= $trangHienTai == 'admin-mail' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-envelope"></i></span><span class="menu-text">Email / Thông báo</span>
                </a>
                <a href="index.php?page=admin-settings" class="menu-item <?= $trangHienTai == 'admin-settings' ? 'active' : '' ?>">
                    <span class="menu-icon"><i class="fas fa-cog"></i></span><span class="menu-text">Cài đặt</span>
                </a>
                <a href="index.php?page=home" class="menu-item" style="color: #f37021 !important; text-decoration: none !important;">
                    <span class="menu-icon"><i class="fas fa-home" style="color: #f37021 !important;"></i></span><span class="menu-text">Xem trang chủ</span>
                </a>
            </nav>
        </aside>
        <!-- Main Content -->
        <div class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <span class="page-title"><?= $tieuDeTrang ?? 'Dashboard' ?></span>
                </div>
                <div class="header-right">


                    <div class="admin-profile">
                        <img src="https://placehold.co/36x36/0a1628/fff?text=AD" alt="Admin">
                        <span class="admin-name">Admin VAB</span>
                        <span class="dropdown-arrow"><i class="fas fa-chevron-down"></i></span>
                        <div class="profile-dropdown">
                            <a href="index.php?page=home"><i class="fas fa-home me-2"></i>Xem trang chủ</a>
                            <a href="index.php?page=admin-settings"><i class="fas fa-cog me-2"></i>Cài đặt</a>
                            <a href="index.php?page=admin-logout"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a>
                        </div>
                    </div>
                </div>
            </header>
            <div class="admin-content">