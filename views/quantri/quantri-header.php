<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tieuDeTrang ?? 'VAB Admin' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="admin-wrapper">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="index.php?page=quantri" class="sidebar-logo">VAB<span>.</span></a>
            <button class="sidebar-close" id="sidebarClose"><i class="fas fa-times"></i></button>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-label">Main</div>
            <a href="index.php?page=quantri-tongquan" class="menu-item <?= $trangHienTai == 'quantri-tongquan' ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fas fa-chart-pie"></i></span><span class="menu-text">Tong quan</span>
            </a>
            <a href="index.php?page=quantri-sanpham" class="menu-item <?= strpos($trangHienTai, 'quantri-sanpham') !== false || strpos($trangHienTai, 'quantri-them-sanpham') !== false || strpos($trangHienTai, 'quantri-sua-sanpham') !== false? 'active' : '' ?>">
                <span class="menu-icon"><i class="fas fa-box"></i></span><span class="menu-text">San pham</span>
            </a>
            <a href="index.php?page=quantri-nhanvien" class="menu-item <?= $trangHienTai == 'quantri-nhanvien' ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fas fa-user-tie"></i></span><span class="menu-text">Nhan vien</span>
            </a>
            <a href="index.php?page=quantri-danhgia" class="menu-item <?= $trangHienTai == 'quantri-danhgia' ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fas fa-star"></i></span><span class="menu-text">Danh gia</span>
            </a>
            <a href="index.php?page=quantri-nguoidung" class="menu-item <?= $trangHienTai == 'quantri-nguoidung' ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fas fa-users"></i></span><span class="menu-text">Khach hang</span>
            </a>
            <a href="index.php?page=admin-vouchers" class="menu-item <?= $trangHienTai == 'admin-vouchers' ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fas fa-ticket-alt"></i></span><span class="menu-text">Mã giảm giá</span>
            </a>
            <a href="index.php?page=quantri-donhang" class="menu-item <?= strpos($trangHienTai, 'quantri-donhang') !== false ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fas fa-shopping-cart"></i></span><span class="menu-text">Don hang</span>
            </a>
            <a href="index.php?page=quantri-doanhthu" class="menu-item <?= $trangHienTai == 'quantri-doanhthu' ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fas fa-chart-line"></i></span><span class="menu-text">Doanh thu</span>
            </a>
            <a href="index.php?page=quantri-caidat" class="menu-item <?= $trangHienTai == 'quantri-caidat' ? 'active' : '' ?>">
                <span class="menu-icon"><i class="fas fa-cog"></i></span><span class="menu-text">Cai dat</span>
            </a>
            <div class="menu-label">Account</div>
            <a href="index.php?page=dangxuat" class="menu-item">
                <span class="menu-icon"><i class="fas fa-sign-out-alt"></i></span><span class="menu-text">Dang xuat</span>
            </a>
        </nav>
    </aside>
    <div class="admin-main">
        <header class="admin-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                <span class="page-title"><?= $tieuDeTrang ?? 'Dashboard' ?></span>
            </div>
            <div class="header-right">
                <div class="header-search">
                    <span class="search-icon"><i class="fas fa-search"></i></span>
                    <input type="text" placeholder="Tim kiem...">
                </div>
                <div class="notification-icon">
                    <i class="far fa-bell"></i><span class="badge">5</span>
                </div>
                <div class="admin-profile">
                    <img src="https://placehold.co/36x36/0a1628/fff?text=AD" alt="Admin">
                    <span class="admin-name">Admin VAB</span>
                    <span class="dropdown-arrow"><i class="fas fa-chevron-down"></i></span>
                    <div class="profile-dropdown">
                        <a href="index.php?page=quantri-caidat"><i class="fas fa-user me-2"></i>Ho so</a>
                        <a href="index.php?page=quantri-caidat"><i class="fas fa-cog me-2"></i>Cai dat</a>
                        <a href="index.php?page=dangxuat"><i class="fas fa-sign-out-alt me-2"></i>Dang xuat</a>
                    </div>
                </div>
            </div>
        </header>
        <div class="admin-content">