-- ============================================================
-- VAB DATABASE - FILE CSDL DUY NHAT
-- Ten bang va ten cot dung tieng Viet khong dau
-- Phien ban: 2.0 | Ngay: 2026-06-16
-- ============================================================

-- ============================================================
-- BANG 1: KHACHHANG (Nguoi dung / Khach hang)
-- ============================================================
CREATE TABLE IF NOT EXISTS `KHACHHANG` (
    `MaKH`         INT AUTO_INCREMENT PRIMARY KEY,
    `HoTenKH`      VARCHAR(255) NOT NULL,
    `email`        VARCHAR(255) NOT NULL UNIQUE,
    `MatKhauKH`    VARCHAR(255) NOT NULL,
    `SDT`          VARCHAR(20) DEFAULT '',
    `TrangThai`    ENUM('active', 'locked') DEFAULT 'active',
    `HangKH`       ENUM('silver', 'gold', 'diamond') DEFAULT 'silver',
    `DiaChi`       TEXT,
    `NgayTao`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANG 2: NHANVIEN (Nhan vien quan tri)
-- ============================================================
CREATE TABLE IF NOT EXISTS `NHANVIEN` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `HoTenNV`      VARCHAR(255) NOT NULL,
    `email`        VARCHAR(255) NOT NULL UNIQUE,
    `MatKhauNV`    VARCHAR(255) NOT NULL,
    `SDT`          VARCHAR(20) DEFAULT '',
    `TrangThai`    ENUM('active', 'locked') DEFAULT 'active',
    `VaiTro`       ENUM('admin', 'manager', 'staff') DEFAULT 'staff',
    `NgayLapTK`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANG 3: DANHMUCSP (Danh muc san pham)
-- ============================================================
CREATE TABLE IF NOT EXISTS `DANHMUCSP` (
    `MaDanhMuc`    INT AUTO_INCREMENT PRIMARY KEY,
    `TenDM`        VARCHAR(255) NOT NULL,
    `DuongDan`     VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANG 4: THUONGHIEU (Thuong hieu san pham)
-- ============================================================
CREATE TABLE IF NOT EXISTS `THUONGHIEU` (
    `MaThuongHieu` INT AUTO_INCREMENT PRIMARY KEY,
    `TenThuongHieu` VARCHAR(255) NOT NULL,
    `DuongDan`     VARCHAR(255) NOT NULL UNIQUE,
    `logo`         VARCHAR(255) DEFAULT '',
    `MoTa`         TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANG 5: SANPHAM (San pham)
-- ============================================================
CREATE TABLE IF NOT EXISTS `SANPHAM` (
    `MaSP`         INT AUTO_INCREMENT PRIMARY KEY,
    `TenSP`        VARCHAR(255) NOT NULL,
    `DuongDan`     VARCHAR(255) NOT NULL UNIQUE,
    `MaDanhMuc`    INT DEFAULT NULL,
    `Gia`          INT NOT NULL,
    `GiaKhuyenMai` INT DEFAULT NULL,
    `AnhChinh`     VARCHAR(255) DEFAULT '',
    `AnhPhu`       TEXT,
    `MoTaSP`       TEXT,
    `MaThuongHieu` INT DEFAULT NULL,
    `TheThao`      VARCHAR(255) DEFAULT '',
    `GioiTinh`     VARCHAR(50) DEFAULT '',
    `KichCo`       VARCHAR(255) DEFAULT '',
    `TrangThai`    TINYINT DEFAULT 1,
    `NgayTao`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`MaDanhMuc`) REFERENCES `DANHMUCSP`(`MaDanhMuc`) ON DELETE SET NULL,
    FOREIGN KEY (`MaThuongHieu`) REFERENCES `THUONGHIEU`(`MaThuongHieu`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANG 6: QUANLYSP (Quan ly kich co va so luong ton kho)
-- (truoc day la product_size)
-- ============================================================
CREATE TABLE IF NOT EXISTS `QUANLYSP` (
    `MaSP`         INT NOT NULL,
    `KichCo`       VARCHAR(50) NOT NULL,
    `SoLuong`      INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`MaSP`, `KichCo`),
    FOREIGN KEY (`MaSP`) REFERENCES `SANPHAM`(`MaSP`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANG 7: DONHANG (Don hang)
-- ============================================================
CREATE TABLE IF NOT EXISTS `DONHANG` (
    `MaDonHang`    INT AUTO_INCREMENT PRIMARY KEY,
    `MaKH`         INT DEFAULT NULL,
    `HoTenKH`      VARCHAR(255) NOT NULL,
    `email`        VARCHAR(255) DEFAULT NULL,
    `SDT`          VARCHAR(20) NOT NULL,
    `DiaChi`       TEXT NOT NULL,
    `PhuongThucTT` VARCHAR(100) DEFAULT 'COD',
    `GhiChu`       TEXT DEFAULT NULL,
    `TongTien`     INT NOT NULL,
    `TrangThaiDH`  ENUM('pending','confirmed','shipping','delivered','completed','cancelled') DEFAULT 'pending',
    `MaGiamGia`    VARCHAR(50) DEFAULT NULL,
    `SoTienGiam`   INT DEFAULT 0,
    `NgayTao`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`MaKH`) REFERENCES `KHACHHANG`(`MaKH`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANG 8: CHITIETDONHANG (Chi tiet don hang)
-- ============================================================
CREATE TABLE IF NOT EXISTS `CHITIETDONHANG` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `MaDonHang`    INT NOT NULL,
    `MaSP`         INT DEFAULT NULL,
    `KichCo`       VARCHAR(50) DEFAULT '',
    `TenSP`        VARCHAR(255) NOT NULL,
    `SoLuongSP`    INT NOT NULL DEFAULT 1,
    `DonGia`       INT NOT NULL,
    FOREIGN KEY (`MaDonHang`) REFERENCES `DONHANG`(`MaDonHang`) ON DELETE CASCADE,
    FOREIGN KEY (`MaSP`) REFERENCES `SANPHAM`(`MaSP`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANG 9: DANHGIASP (Danh gia san pham)
-- ============================================================
CREATE TABLE IF NOT EXISTS `DANHGIASP` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `MaSP`         INT NOT NULL,
    `MaKH`         INT DEFAULT NULL,
    `TenKH`        VARCHAR(255) NOT NULL,
    `email`        VARCHAR(255) DEFAULT '',
    `SoSao`        TINYINT NOT NULL DEFAULT 5,
    `NoiDung`      TEXT,
    `TrangThai`    ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `NgayDG`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `chk_sao` CHECK (`SoSao` >= 1 AND `SoSao` <= 5),
    FOREIGN KEY (`MaSP`) REFERENCES `SANPHAM`(`MaSP`) ON DELETE CASCADE,
    FOREIGN KEY (`MaKH`) REFERENCES `KHACHHANG`(`MaKH`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANG 10: MAGIAMGIA (Ma giam gia / Voucher)
-- ============================================================
CREATE TABLE IF NOT EXISTS `MAGIAMGIA` (
    `id`                INT AUTO_INCREMENT PRIMARY KEY,
    `MaGiamGia`         VARCHAR(50) NOT NULL UNIQUE,
    `Ten`               VARCHAR(255) NOT NULL,
    `Loai`              ENUM('percent', 'fixed') DEFAULT 'percent',
    `GiaTri`            INT NOT NULL COMMENT 'Gia tri giam (% hoac so tien)',
    `DonHangToiThieu`   INT DEFAULT 0,
    `SoLuot`            INT DEFAULT 100 COMMENT 'So luot toi da',
    `DaDung`            INT DEFAULT 0 COMMENT 'So luot da su dung',
    `NgayBD`            DATE NOT NULL,
    `NgayKT`            DATE NOT NULL,
    `TrangThai`         ENUM('active', 'inactive', 'expired') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BANG 11: THONGBAO (Thong bao / Hop thu)
-- ============================================================
CREATE TABLE IF NOT EXISTS `THONGBAO` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `MaKH`         INT NOT NULL,
    `TieuDe`       VARCHAR(255) NOT NULL,
    `NoiDung`      TEXT NOT NULL,
    `TrangThai`    TINYINT DEFAULT 0 COMMENT '0=chua doc, 1=da doc',
    `NgayTao`      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`MaKH`) REFERENCES `KHACHHANG`(`MaKH`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DU LIEU MAU - DANH MUC
-- ============================================================
INSERT INTO `DANHMUCSP` (`TenDM`, `DuongDan`) VALUES
('Ao', 'ao'),
('Quan', 'quan'),
('Giay', 'giay'),
('Phu kien', 'phu-kien');

-- ============================================================
-- DU LIEU MAU - THUONG HIEU
-- ============================================================
INSERT INTO `THUONGHIEU` (`TenThuongHieu`, `DuongDan`, `MoTa`) VALUES
('Nike', 'nike', 'Thuong hieu the thao so 1 the gioi'),
('Adidas', 'adidas', 'Thuong hieu the thao Duc noi tieng'),
('Puma', 'puma', 'Thuong hieu the thao toan cau');

-- ============================================================
-- DU LIEU MAU - NHAN VIEN (mat khau mac dinh: 123456)
-- Hash: $2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im
-- ============================================================
INSERT INTO `NHANVIEN` (`HoTenNV`, `email`, `MatKhauNV`, `SDT`, `VaiTro`) VALUES
('Admin VAB', 'admin@vab.com', '$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im', '0903456789', 'admin'),
('Quan ly Nguyen', 'manager@vab.com', '$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im', '0909999999', 'manager'),
('Nhan vien Tran', 'staff@vab.com', '$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im', '0908888888', 'staff');

-- ============================================================
-- DU LIEU MAU - SAN PHAM
-- ============================================================
INSERT INTO `SANPHAM` (`TenSP`, `DuongDan`, `MaDanhMuc`, `Gia`, `GiaKhuyenMai`, `MaThuongHieu`, `TheThao`, `GioiTinh`, `KichCo`, `MoTaSP`) VALUES
('Ao The Thao Nam Nike Dri-FIT',      'ao-the-thao-nam-nike-dri-fit',      1, 790000, NULL,    1, 'Chay bo',  'Nam',    'S,M,L,XL',     'Ao the thao nam Nike Dri-FIT chat lieu cao cap'),
('Quan Short The Thao Nam Adidas',    'quan-short-the-thao-nam-adidas',    2, 550000, NULL,    2, 'Chay bo',  'Nam',    'S,M,L',        'Quan short the thao nam Adidas thoai mai'),
('Giay Chay Bo Puma Ultra Run',       'giay-chay-bo-puma-ultra-run',       3, 2450000, NULL,   3, 'Chay bo',  'Nam',    '40,41,42,43',  'Giay chay bo Puma Ultra Run'),
('Ao Khoac Gio The Thao Unisex',      'ao-khoac-gio-the-thao-unisex',      1, 1290000, NULL,   1, 'Chay bo',  'Unisex', 'S,M,L,XL',     'Ao khoac gio the thao unisex chong nuoc'),
('Ao Ba Lo Gym Nike Pro',             'ao-ba-lo-gym-nike-pro',             1, 650000, NULL,    1, 'Gym',      'Nam',    'S,M,L',        'Ao ba lo gym Nike Pro'),
('Ao Bong Da Adidas Real Madrid',     'ao-bong-da-adidas-real-madrid',     1, 1150000, NULL,   2, 'Bong da',  'Nam',    'S,M,L,XL',     'Ao bong da Adidas Real Madrid'),
('Giay Tennis NikeCourt Air Zoom',    'giay-tennis-nikecourt-air-zoom',    3, 3290000, NULL,   1, 'Tennis',   'Nam',    '40,41,42,43',  'Giay tennis NikeCourt Air Zoom'),
('Quan Dai Gym Adidas Tiro',          'quan-dai-gym-adidas-tiro',          2, 890000, NULL,    2, 'Gym',      'Nam',    'S,M,L,XL',     'Quan dai gym Adidas Tiro'),
('Balo The Thao Adidas',              'balo-the-thao-adidas',              4, 890000, NULL,    2, 'Chay bo',  'Unisex', 'OS',           'Balo the thao Adidas'),
('Quan Jogger The Thao Nam',          'quan-jogger-the-thao-nam',          2, 690000, NULL,    1, 'Chay bo',  'Nam',    'S,M,L',        'Quan jogger the thao nam phong cach');

-- ============================================================
-- DU LIEU MAU - QUAN LY KICH CO (QUANLYSP)
-- ============================================================
INSERT INTO `QUANLYSP` (`MaSP`, `KichCo`, `SoLuong`) VALUES
(1, 'S', 15), (1, 'M', 20), (1, 'L', 10), (1, 'XL', 5),
(2, 'S', 10), (2, 'M', 15), (2, 'L', 5),
(3, '40', 5),  (3, '41', 8),  (3, '42', 5),  (3, '43', 2),
(4, 'S', 5),  (4, 'M', 5),  (4, 'L', 3),  (4, 'XL', 2),
(5, 'S', 1),  (5, 'M', 1),
(6, 'S', 8),  (6, 'M', 10), (6, 'L', 5),  (6, 'XL', 2),
(7, '40', 3),  (7, '41', 4),  (7, '42', 2),  (7, '43', 1),
(8, 'S', 5),  (8, 'M', 8),  (8, 'L', 4),  (8, 'XL', 1),
(9, 'OS', 5),
(10, 'S', 3), (10, 'M', 3), (10, 'L', 2);

-- ============================================================
-- DU LIEU MAU - MA GIAM GIA
-- ============================================================
INSERT INTO `MAGIAMGIA` (`MaGiamGia`, `Ten`, `Loai`, `GiaTri`, `DonHangToiThieu`, `SoLuot`, `DaDung`, `NgayBD`, `NgayKT`, `TrangThai`) VALUES
('VAB10',   'Giam 10% don hang',         'percent', 10,    500000,  100, 15, '2025-01-01', '2025-12-31', 'active'),
('VAB50K',  'Giam 50.000d',              'fixed',   50000, 300000,  50,  8,  '2025-01-01', '2025-06-30', 'active'),
('SALE20',  'Giam 20% the thao',         'percent', 20,    1000000, 30,  3,  '2025-03-01', '2025-05-31', 'active'),
('WELCOME', 'Giam 15% cho thanh vien moi','percent', 15,   0,       200, 45, '2025-01-01', '2025-12-31', 'active'),
('SUMMER',  'Summer Sale 25%',           'percent', 25,    2000000, 20,  0,  '2025-06-01', '2025-08-31', 'active');
