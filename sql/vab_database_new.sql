-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: vab
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `chitietdonhang`
--

DROP TABLE IF EXISTS `chitietdonhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chitietdonhang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `MaDonHang` int(11) NOT NULL,
  `MaSP` int(11) DEFAULT NULL,
  `KichCo` varchar(50) DEFAULT '',
  `TenSP` varchar(255) NOT NULL,
  `SoLuongSP` int(11) NOT NULL DEFAULT 1,
  `DonGia` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `MaDonHang` (`MaDonHang`),
  KEY `MaSP` (`MaSP`),
  CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`MaDonHang`) REFERENCES `donhang` (`MaDonHang`) ON DELETE CASCADE,
  CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chitietdonhang`
--

LOCK TABLES `chitietdonhang` WRITE;
/*!40000 ALTER TABLE `chitietdonhang` DISABLE KEYS */;
INSERT INTO `chitietdonhang` VALUES (1,1,NULL,'S','Ao The Thao Nam Nike Dri-FIT',1,790000),(2,2,NULL,'S','Ao The Thao Nam Nike Dri-FIT',1,790000),(3,3,NULL,'39','Giày Thể Thao Chạy Bộ Nam Nike Run Defy',1,1500000),(4,4,NULL,'39','Giày Thể Thao Chạy Bộ Nam Nike Run Defy',1,1500000),(5,5,NULL,'39','Giày Thể Thao Chạy Bộ Nam Nike Run Defy',1,1500000),(6,6,14,'39','Giày Thể Thao Tập Luyện Nữ Nike W Nike Flex Train',1,500000),(7,7,14,'39','Giày Thể Thao Tập Luyện Nữ Nike W Nike Flex Train',1,500000),(8,8,15,'39','Giày Thể Thao Chạy Bộ Nữ Nike W Nike Vomero ',1,600000),(9,9,19,'L','áo',1,10000),(10,10,16,'S','Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr',1,2000),(12,12,13,'M','Product 1',1,600000);
/*!40000 ALTER TABLE `chitietdonhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `danhgiasp`
--

DROP TABLE IF EXISTS `danhgiasp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `danhgiasp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `MaSP` int(11) NOT NULL,
  `MaKH` int(11) DEFAULT NULL,
  `TenKH` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT '',
  `SoSao` tinyint(4) NOT NULL DEFAULT 5,
  `NoiDung` text DEFAULT NULL,
  `TrangThai` enum('pending','approved','rejected') DEFAULT 'pending',
  `NgayDG` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `MaSP` (`MaSP`),
  KEY `MaKH` (`MaKH`),
  CONSTRAINT `danhgiasp_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE,
  CONSTRAINT `danhgiasp_ibfk_2` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE SET NULL,
  CONSTRAINT `chk_sao` CHECK (`SoSao` >= 1 and `SoSao` <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `danhgiasp`
--

LOCK TABLES `danhgiasp` WRITE;
/*!40000 ALTER TABLE `danhgiasp` DISABLE KEYS */;
/*!40000 ALTER TABLE `danhgiasp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `danhmucsp`
--

DROP TABLE IF EXISTS `danhmucsp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `danhmucsp` (
  `MaDanhMuc` int(11) NOT NULL AUTO_INCREMENT,
  `TenDM` varchar(255) NOT NULL,
  `DuongDan` varchar(255) NOT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`MaDanhMuc`),
  UNIQUE KEY `DuongDan` (`DuongDan`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `danhmucsp`
--

LOCK TABLES `danhmucsp` WRITE;
/*!40000 ALTER TABLE `danhmucsp` DISABLE KEYS */;
INSERT INTO `danhmucsp` VALUES (1,'Ao','ao',1),(2,'Quan','quan',1),(3,'Giay','giay',1),(4,'Phu kien','phu-kien',1);
/*!40000 ALTER TABLE `danhmucsp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donhang`
--

DROP TABLE IF EXISTS `donhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `donhang` (
  `MaDonHang` int(11) NOT NULL AUTO_INCREMENT,
  `MaKH` int(11) DEFAULT NULL,
  `HoTenKH` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `SDT` varchar(20) NOT NULL,
  `DiaChi` text NOT NULL,
  `PhuongThucTT` varchar(100) DEFAULT 'COD',
  `GhiChu` text DEFAULT NULL,
  `TongTien` int(11) NOT NULL,
  `TrangThaiDH` enum('pending','confirmed','shipping','delivered','completed','cancelled') DEFAULT 'pending',
  `MaGiamGia` varchar(50) DEFAULT NULL,
  `SoTienGiam` int(11) DEFAULT 0,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`MaDonHang`),
  KEY `MaKH` (`MaKH`),
  CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donhang`
--

LOCK TABLES `donhang` WRITE;
/*!40000 ALTER TABLE `donhang` DISABLE KEYS */;
INSERT INTO `donhang` VALUES (1,2,'Test Customer','admin@vab.com','0987654321','123 Test Street, Hanoi','sepay','Deliver during office hours',790000,'confirmed',NULL,0,'2026-06-15 19:53:09'),(2,2,'Test Customer','admin@vab.com','0987654321','123 Test Street, Hanoi','sepay','Deliver during office hours',790000,'confirmed',NULL,0,'2026-06-15 20:02:37'),(3,2,'Test Customer','admin@vab.com','0987654321','123 Test Street, Hanoi','sepay','Deliver during office hours',1500000,'confirmed',NULL,0,'2026-06-15 20:12:28'),(4,2,'Test Customer','admin@vab.com','0987654321','123 Test Street, Hanoi','sepay','Deliver during office hours',1500000,'confirmed',NULL,0,'2026-06-15 20:20:00'),(5,2,'Test Customer','admin@vab.com','0987654321','123 Test Street, Hanoi','sepay','Deliver during office hours',1500000,'confirmed',NULL,0,'2026-06-15 20:22:56'),(6,1,'trương chí vỹ','truongvy2762005@gmail.com','0123456789','110d tân quý, tân phú, HCM','COD','',500000,'completed',NULL,0,'2026-06-16 00:35:12'),(7,1,'trương chí vỹ','truongvy2762005@gmail.com','0123456789','110d tân quý, tân phú, HCM','COD','',500000,'completed',NULL,0,'2026-06-16 00:57:58'),(8,1,'trương chí vỹ','truongvy2762005@gmail.com','0123456789','110d tân quý, tân phú, HCM','sepay','',600000,'shipping',NULL,0,'2026-06-16 01:05:32'),(9,1,'trương chí vỹ','truongvy2762005@gmail.com','0123456789','110d tân quý, tân phú, HCM','COD','',10000,'completed',NULL,0,'2026-06-16 01:59:43'),(10,1,'trương chí vỹ','truongvy2762005@gmail.com','0123456789','110d tân quý, tân phú, HCM','sepay','',2000,'confirmed',NULL,0,'2026-06-16 03:15:35'),(11,1,'Test Customer','testcustomer@example.com','0987654321','123 Test Street','COD','Test coupon order',540000,'pending','VAB10',60000,'2026-06-16 03:50:18'),(12,1,'Test Customer','testcustomer@example.com','0987654321','123 Test Street','COD','Test coupon order',540000,'completed','VAB10',60000,'2026-06-16 03:50:49');
/*!40000 ALTER TABLE `donhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `khachhang`
--

DROP TABLE IF EXISTS `khachhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `khachhang` (
  `MaKH` int(11) NOT NULL AUTO_INCREMENT,
  `HoTenKH` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `MatKhauKH` varchar(255) NOT NULL,
  `SDT` varchar(20) DEFAULT '',
  `TrangThai` enum('active','locked') DEFAULT 'active',
  `HangKH` enum('silver','gold','diamond') DEFAULT 'silver',
  `DiaChi` text DEFAULT NULL,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`MaKH`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `khachhang`
--

LOCK TABLES `khachhang` WRITE;
/*!40000 ALTER TABLE `khachhang` DISABLE KEYS */;
INSERT INTO `khachhang` VALUES (1,'Vỹ kC','truongvy2762005@gmail.com','$2y$10$lXEh3tF4MLO5umUUH6gvmeZs5/.dyZ6SO7knFI9eoEXsdeEsrlzEO','','active','silver',NULL,'2026-06-15 18:39:34'),(2,'Admin VAB','admin@vab.com','$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im','0903456789','active','silver',NULL,'2026-06-15 19:53:09');
/*!40000 ALTER TABLE `khachhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `magiamgia`
--

DROP TABLE IF EXISTS `magiamgia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `magiamgia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `MaGiamGia` varchar(50) NOT NULL,
  `Ten` varchar(255) NOT NULL,
  `Loai` enum('percent','fixed') DEFAULT 'percent',
  `GiaTri` int(11) NOT NULL COMMENT 'Gia tri giam (% hoac so tien)',
  `DonHangToiThieu` int(11) DEFAULT 0,
  `SoLuot` int(11) DEFAULT 100 COMMENT 'So luot toi da',
  `DaDung` int(11) DEFAULT 0 COMMENT 'So luot da su dung',
  `NgayBD` date NOT NULL,
  `NgayKT` date NOT NULL,
  `TrangThai` enum('active','inactive','expired') DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `MaGiamGia` (`MaGiamGia`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `magiamgia`
--

LOCK TABLES `magiamgia` WRITE;
/*!40000 ALTER TABLE `magiamgia` DISABLE KEYS */;
INSERT INTO `magiamgia` VALUES (1,'VAB10','Giam 10% don hang','percent',10,500000,100,16,'2025-01-01','2028-12-31','active'),(2,'VAB50K','Giam 50.000d','fixed',50000,300000,50,8,'2025-01-01','2025-06-30','active'),(3,'SALE20','Giam 20% the thao','percent',20,1000000,30,3,'2025-03-01','2025-05-31','active'),(4,'WELCOME','Giam 15% cho thanh vien moi','percent',15,0,200,45,'2025-01-01','2025-12-31','active'),(5,'SUMMER','Summer Sale 25%','percent',25,2000000,20,0,'2025-06-01','2025-08-31','active'),(6,'GIAMGIA','giảm giá ','percent',10,0,100,0,'2026-06-16','2026-07-16','active');
/*!40000 ALTER TABLE `magiamgia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nhanvien`
--

DROP TABLE IF EXISTS `nhanvien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nhanvien` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `HoTenNV` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `MatKhauNV` varchar(255) NOT NULL,
  `SDT` varchar(20) DEFAULT '',
  `TrangThai` enum('active','locked') DEFAULT 'active',
  `VaiTro` enum('admin','manager','staff') DEFAULT 'staff',
  `NgayLapTK` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nhanvien`
--

LOCK TABLES `nhanvien` WRITE;
/*!40000 ALTER TABLE `nhanvien` DISABLE KEYS */;
INSERT INTO `nhanvien` VALUES (1,'Admin VAB','admin@vab.com','$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im','0903456789','active','admin','2026-06-15 18:25:10'),(2,'Quan ly Nguyen','manager@vab.com','$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im','0909999999','active','manager','2026-06-15 18:25:10'),(3,'Nhan vien Tran','staff@vab.com','$2y$10$WKXyGL/iwnyE5eQY2wS4aupJeHcZAAA8XtZvLvoEFx138.69xX5im','0908888888','active','staff','2026-06-15 18:25:10');
/*!40000 ALTER TABLE `nhanvien` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quanlysp`
--

DROP TABLE IF EXISTS `quanlysp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `quanlysp` (
  `MaSP` int(11) NOT NULL,
  `KichCo` varchar(50) NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`MaSP`,`KichCo`),
  CONSTRAINT `quanlysp_ibfk_1` FOREIGN KEY (`MaSP`) REFERENCES `sanpham` (`MaSP`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quanlysp`
--

LOCK TABLES `quanlysp` WRITE;
/*!40000 ALTER TABLE `quanlysp` DISABLE KEYS */;
INSERT INTO `quanlysp` VALUES (13,'39',10),(13,'40',10),(13,'41',10),(13,'42',10),(14,'39',8),(14,'40',10),(14,'41',10),(14,'42',10),(15,'39',9),(15,'40',10),(15,'41',10),(15,'42',10),(16,'L',0),(16,'M',0),(16,'S',3),(19,'L',9),(19,'M',10),(19,'S',10),(19,'XL',10),(20,'L',10),(20,'M',10),(20,'S',10),(20,'XL',10),(21,'L',10),(21,'M',10),(21,'S',10),(21,'XL',10),(22,'L',10),(22,'M',10),(22,'S',10),(22,'XL',7),(23,'L',10),(23,'M',10),(23,'S',10),(23,'XL',10),(24,'L',7),(24,'M',5),(24,'S',3),(24,'XL',8),(25,'L',10),(25,'M',10),(25,'S',10),(25,'XL',10),(26,'L',10),(26,'M',10),(26,'S',10),(26,'XL',10),(27,'L',10),(27,'M',10),(27,'S',10),(27,'XL',10),(28,'L',8),(28,'M',15),(28,'S',20),(28,'XL',9);
/*!40000 ALTER TABLE `quanlysp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sanpham`
--

DROP TABLE IF EXISTS `sanpham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sanpham` (
  `MaSP` int(11) NOT NULL AUTO_INCREMENT,
  `TenSP` varchar(255) NOT NULL,
  `DuongDan` varchar(255) NOT NULL,
  `MaDanhMuc` int(11) DEFAULT NULL,
  `Gia` int(11) NOT NULL,
  `GiaKhuyenMai` int(11) DEFAULT NULL,
  `AnhChinh` varchar(255) DEFAULT '',
  `AnhPhu` text DEFAULT NULL,
  `MoTaSP` text DEFAULT NULL,
  `MaThuongHieu` int(11) DEFAULT NULL,
  `TheThao` varchar(255) DEFAULT '',
  `GioiTinh` varchar(50) DEFAULT '',
  `KichCo` varchar(255) DEFAULT '',
  `TrangThai` tinyint(4) DEFAULT 1,
  `NgayTao` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`MaSP`),
  UNIQUE KEY `DuongDan` (`DuongDan`),
  KEY `MaDanhMuc` (`MaDanhMuc`),
  KEY `MaThuongHieu` (`MaThuongHieu`),
  CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`MaDanhMuc`) REFERENCES `danhmucsp` (`MaDanhMuc`) ON DELETE SET NULL,
  CONSTRAINT `sanpham_ibfk_2` FOREIGN KEY (`MaThuongHieu`) REFERENCES `thuonghieu` (`MaThuongHieu`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sanpham`
--

LOCK TABLES `sanpham` WRITE;
/*!40000 ALTER TABLE `sanpham` DISABLE KEYS */;
INSERT INTO `sanpham` VALUES (13,'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Nam Nike Tiempo Maestro Academy','gi-y-th-thao-b-ng-s-n-c-t-nhi-n-nam-nike-tiempo-maestro-academy',3,700000,NULL,'1781555373_giay3a.webp','1781555373_sub_0_giay3d.webp,1781555373_sub_1_giay3c.webp,1781555373_sub_2_giay3b.webp','Tiempo Maestro Academy giúp bạn kiểm soát từng pha chạm bóng. Được chế tác với da FlyTouch, bề mặt mềm mại mang lại cảm giác bóng chính xác, giúp bạn dễ dàng làm chủ trận đấu và gây áp lực lên mọi hàng phòng ngự.\r\nCảm giác chạm bóng mềm mại\r\nPhần upper FlyTouch nhẹ mang lại cảm giác chạm bóng ổn định trong cả điều kiện ướt hoặc khô.\r\nĐộ bám cho nhiều loại sân\r\nĐinh dạng lưỡi (bladed studs) ở gót và mũi giày giúp bám sân tốt trên cả cỏ tự nhiên và cỏ nhân tạo. Đinh hình nón (conical studs) hỗ trợ bạn trụ và xoay người dễ dàng.',1,'Bóng đá','Nam','39,40,41,42',1,'2026-06-15 20:29:33'),(14,'Giày Thể Thao Tập Luyện Nữ Nike W Nike Flex Train','gi-y-th-thao-t-p-luy-n-n-nike-w-nike-flex-train',3,500000,NULL,'1781555699_giay4a.webp','1781555699_sub_0_giay4d.webp,1781555699_sub_1_giay4c.webp,1781555699_sub_2_giay4b.webp','Nâng. Tăng tốc. Vận động. Với Nike Flex Train, bạn có toàn quyền lựa chọn hành trình tập luyện của riêng mình. Đế ngoài linh hoạt kết hợp cùng thân giày nhẹ giúp mở ra nhiều lựa chọn tập luyện đa dạng.',1,'Gym','Nữ','39,40,41,42',1,'2026-06-15 20:34:59'),(15,'Giày Thể Thao Chạy Bộ Nữ Nike W Nike Vomero ','gi-y-th-thao-ch-y-b-n-nike-w-nike-vomero-',3,600000,NULL,'1781555870_giay5a.webp','1781555870_sub_0_giay5d.webp,1781555870_sub_1_giay5c.webp,1781555870_sub_2_giay5b.webp','Đệm tối đa của Vomero mang lại cảm giác êm ái cho những bước chạy thường ngày. Đây là trải nghiệm mềm mại, được đệm dày nhất của chúng tôi, với lớp ZoomX nhẹ trên cùng kết hợp cùng ReactX phản hồi nhanh trong đế giữa. Ngoài ra, họa tiết đế mới còn giúp chuyển động gót - mũi chân thêm mượt mà.',1,'Chạy bộ','Nữ','39,40,41,42',1,'2026-06-15 20:37:50'),(16,'Vớ Thể Thao Unisex Nike U Nk Nsw Everyday Essential Cr','v-th-thao-unisex-nike-u-nk-nsw-everyday-essential-cr',4,2000,NULL,'1781573174_vo1.webp','1781573174_sub_0_vo4.jpg,1781573174_sub_1_vo3.webp,1781573174_sub_2_vo2.webp','Công nghệ Dri-FIT giúp giữ cho bàn chân khô ráo và thoải mái.',1,'Bóng đá','Nam','L,M,S',1,'2026-06-16 01:26:14'),(19,'áo','-o',1,10000,NULL,'1781575166_ao3lo.webp','1781575166_sub_0_ao3lo2.jpg,1781575166_sub_1_ao3lo1.webp','',1,'Chạy bộ','Nam','S,M,L,XL',1,'2026-06-16 01:59:26'),(20,'Quần Ngắn Thể Thao Nam Nike As M Nk Df Challenger 7Bf Sh','qu-n-ng-n-th-thao-nam-nike-as-m-nk-df-challenger-7bf-sh',2,250000,NULL,'1781625208_quan1.webp','1781625208_sub_0_quan4.webp,1781625208_sub_1_quan3.webp,1781625208_sub_2_quan2.webp','Được thiết kế cho chạy bộ, tập luyện và yoga, quần short Challenger thấm hút mồ hôi giúp bạn luôn nhẹ nhàng, thoáng mát với phom rộng rãi, hỗ trợ tối đa chuyển động. Không chỉ dành riêng cho chạy bộ, quần còn có túi tiện lợi được bố trí để không gây khó chịu khi bạn chuyển từ đường chạy sang phòng gym.',1,'Gym','Nam','S,M,L,XL',1,'2026-06-16 15:53:28'),(21,'Áo Tay Ngắn Thể Thao Bóng Đá Trẻ Em Unisex Nike Fcb Y Nk Df Jsy Ss Stad Hm','-o-tay-ng-n-th-thao-b-ng-tr-em-unisex-nike-fcb-y-nk-df-jsy-ss-stad-hm',1,200000,NULL,'1781625823_ao3a.webp','1781625823_sub_0_ao3d.webp,1781625823_sub_1_ao3c.webp,1781625823_sub_2_ao3b.webp','Áo sân nhà F.C. Barcelona 2025/26 mang đến diện mạo mới cho một trong những thiết kế mang tính biểu tượng nhất của bóng đá thế giới. Sắc Blaugrana huyền thoại được kết hợp cùng các họa tiết lấy cảm hứng từ chuyển động và điểm nhấn màu sắc tươi sáng, tràn đầy năng lượng – tôn vinh sự đoàn kết và lòng trung thành của người hâm mộ dành cho CLB.',1,'Bóng đá','Nam','S,M,L,XL',1,'2026-06-16 16:03:43'),(22,'Quần Ngắn Thể Thao Bóng Đá Nam Nike Cfc M Nk Df Strk Short Kz3R','qu-n-ng-n-th-thao-b-ng-nam-nike-cfc-m-nk-df-strk-short-kz3r',2,200000,NULL,'1781626082_quan4a.webp','1781626082_sub_0_quan5d.webp,1781626082_sub_1_quan4c.webp,1781626082_sub_2_quan4b.webp','Với những chi tiết thiết kế được tinh chỉnh riêng cho các ngôi sao bóng đá tương lai, phom dáng cổ điển kết hợp công nghệ thấm hút mồ hôi giúp bạn luôn mát mẻ và tự tin khi rèn luyện kỹ năng.',1,'Bóng đá','Nam','L,M,S,XL',1,'2026-06-16 16:07:20'),(23,'Quần Ngắn Thể Thao Bóng Đá Nam Nike Fcb M Nk Df Strk Short Kz 3R','qu-n-ng-n-th-thao-b-ng-nam-nike-fcb-m-nk-df-strk-short-kz-3r',2,150000,NULL,'1781626732_quan6b.webp','1781626255_sub_0_quan6d.webp,1781626255_sub_1_quan6c.webp,1781626255_sub_2_quan6b.webp','Với những chi tiết thiết kế được tinh chỉnh riêng cho các ngôi sao bóng đá trẻ, phom dáng cổ điển cùng công nghệ thấm hút mồ hôi giúp bạn luôn mát mẻ và tự tin khi rèn luyện kỹ năng',1,'Bóng đá','Nam','L,M,S,XL',1,'2026-06-16 16:10:55'),(24,'Áo Tay Ngắn Thể Thao Bóng Đá Nam Nike Fcb M Nk Df Strk Ss Top K 3R','-o-tay-ng-n-th-thao-b-ng-nam-nike-fcb-m-nk-df-strk-ss-top-k-3r',1,100000,NULL,'1781626434_ao4a.webp','1781626434_sub_0_ao4d.webp,1781626434_sub_1_ao4c.webp,1781626434_sub_2_ao4b.webp','Với những chi tiết thiết kế được tạo ra riêng cho các ngôi sao bóng đá đang lên, dáng ôm cùng công nghệ thấm hút mồ hôi giúp bạn luôn mát mẻ và giữ sự tự tin khi rèn giũa kỹ năng.',1,'Bóng đá','Nam','L,M,S,XL',1,'2026-06-16 16:12:59'),(25,'Áo ba lỗ thể thao Tập Luyện Nam Nike AS M NK DF TEE STD SLVLS FLEX','-o-ba-l-th-thao-t-p-luy-n-nam-nike-as-m-nk-df-tee-std-slvls-flex',1,100000,NULL,'1781626671_ao7a.webp','1781626671_sub_0_ao7c.webp,1781626671_sub_1_ao7b.webp','',1,'Gym','Nam','S,M,L,XL',1,'2026-06-16 16:17:51'),(26,'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Nam Nike Zoom Vapor 16 Academy Fg/Mg','gi-y-th-thao-b-ng-s-n-c-t-nhi-n-nam-nike-zoom-vapor-16-academy-fg-mg',3,120000,NULL,'1781626883_giay4a.webp','1781626883_sub_0_giay4d.webp,1781626883_sub_1_giay4c.webp,1781626883_sub_2_giay4b.webp','',1,'Bóng đá','Nam','S,M,L,XL',1,'2026-06-16 16:21:23'),(27,'Giày Thể Thao Bóng Đá Sân Cỏ Tự Nhiên Trẻ Em Unisex Nike Jr Legend 10 Club Fg/Mgv','gi-y-th-thao-b-ng-s-n-c-t-nhi-n-tr-em-unisex-nike-jr-legend-10-club-fg-mgv',3,100000,NULL,'1781627018_giay5a.webp','1781627018_sub_0_giay5e.webp,1781627018_sub_1_giay5d.webp,1781627018_sub_2_giay5c.webp','',1,'Bóng đá','Nam','S,M,L,XL',1,'2026-06-16 16:23:38'),(28,'Áo Tay Ngắn Thể Thao Tập Luyện Nam Nike As M Nk Df Tee Std Flex','-o-tay-ng-n-th-thao-t-p-luy-n-nam-nike-as-m-nk-df-tee-std-flex',1,180000,NULL,'1781627213_ao8a.webp','1781627213_sub_0_ao8d.webp,1781627213_sub_1_ao8c.webp,1781627213_sub_2_ao8b.webp','',1,'Gym','Nam','S,M,L,XL',1,'2026-06-16 16:26:53');
/*!40000 ALTER TABLE `sanpham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thethao`
--

DROP TABLE IF EXISTS `thethao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thethao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TenSport` varchar(255) NOT NULL,
  `DuongDan` varchar(255) NOT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `DuongDan` (`DuongDan`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thethao`
--

LOCK TABLES `thethao` WRITE;
/*!40000 ALTER TABLE `thethao` DISABLE KEYS */;
INSERT INTO `thethao` VALUES (1,'Chạy bộ','sport-running',1),(2,'Gym','sport-gym',1),(3,'Bóng đá','sport-football',1),(4,'Bóng rổ','sport-basketball',1),(5,'Tennis','sport-tennis',1);
/*!40000 ALTER TABLE `thethao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thongbao`
--

DROP TABLE IF EXISTS `thongbao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thongbao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `MaKH` int(11) NOT NULL,
  `TieuDe` varchar(255) NOT NULL,
  `NoiDung` text NOT NULL,
  `TrangThai` tinyint(4) DEFAULT 0 COMMENT '0=chua doc, 1=da doc',
  `NgayTao` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `MaKH` (`MaKH`),
  CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`MaKH`) REFERENCES `khachhang` (`MaKH`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thongbao`
--

LOCK TABLES `thongbao` WRITE;
/*!40000 ALTER TABLE `thongbao` DISABLE KEYS */;
/*!40000 ALTER TABLE `thongbao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thuonghieu`
--

DROP TABLE IF EXISTS `thuonghieu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thuonghieu` (
  `MaThuongHieu` int(11) NOT NULL AUTO_INCREMENT,
  `TenThuongHieu` varchar(255) NOT NULL,
  `DuongDan` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT '',
  `MoTa` text DEFAULT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`MaThuongHieu`),
  UNIQUE KEY `DuongDan` (`DuongDan`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thuonghieu`
--

LOCK TABLES `thuonghieu` WRITE;
/*!40000 ALTER TABLE `thuonghieu` DISABLE KEYS */;
INSERT INTO `thuonghieu` VALUES (1,'Nike','nike','','Thuong hieu the thao so 1 the gioi',1),(2,'Adidas','adidas','','Thuong hieu the thao Duc noi tieng',1),(3,'Puma','puma','','Thuong hieu the thao toan cau',1);
/*!40000 ALTER TABLE `thuonghieu` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-17  3:41:08
