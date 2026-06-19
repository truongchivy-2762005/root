<?php
/**
 * ProductModel - Tuong tac voi bang SANPHAM va QUANLYSP
 */

if (!class_exists('xl_database')) {
    require './models/xl_database.php';
}

class ProductModel extends xl_database {

    /** Lay tat ca san pham dang ban */
    public function getAll(): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1) 
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC";
        return $this->layTatCa($sql);
    }

    /** Lay san pham theo MaSP */
    public function getById(int $id): ?array {
        $sql = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.MaSP = ? 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        return $this->layMot($sql, [$id]);
    }

    /** Lay san pham theo danh muc (ten danh muc text) */
    public function getByCategory(string $category): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE (dm.TenDM = ? OR dm.DuongDan = ?) 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC";
        return $this->layTatCa($sql, [$category, $category]);
    }

    /** Lay san pham theo gioi tinh (bao gom ca Unisex) */
    public function getByGender(string $gender): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE (sp.GioiTinh = ? OR sp.GioiTinh = 'Unisex') 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC";
        return $this->layTatCa($sql, [$gender]);
    }

    /** Lay san pham theo thuong hieu */
    public function getByBrand(string $brand): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE (th.TenThuongHieu = ? OR th.DuongDan = ?) 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC";
        return $this->layTatCa($sql, [$brand, $brand]);
    }

    /** Lay san pham theo bo mon the thao */
    public function getBySport(string $sport): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE sp.TheThao = ? 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC";
        return $this->layTatCa($sql, [$sport]);
    }

    /** Lay san pham dang giam gia */
    public function getSaleItems(): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE sp.GiaKhuyenMai IS NOT NULL AND sp.GiaKhuyenMai > 0 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 LIMIT 8";
        return $this->layTatCa($sql);
    }

    /** Lay san pham moi nhat */
    public function getNewProducts(int $limit = 4): array {
        $sql = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.TrangThai = 1 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                ORDER BY sp.NgayTao DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return $this->layTatCa($sql);
    }

    /** Lay URL anh san pham */
    public static function getImageUrl(array $product, int $w = 400, int $h = 500): string {
        $img = $product['AnhChinh'] ?? '';
        if (!empty($img)) {
            if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0 || strpos($img, 'assets/') === 0) {
                return $img;
            }
            return 'assets/images/products/' . $img;
        }
        $id = (int)($product['MaSP'] ?? 0);
        return "https://placehold.co/{$w}x{$h}/0a1628/fff?text=SP+{$id}";
    }

    /** Gia hien thi (uu tien GiaKhuyenMai) */
    public static function getDisplayPrice(array $product): int {
        $giaKM = $product['GiaKhuyenMai'] ?? null;
        if (!empty($giaKM) && $giaKM > 0) {
            return (int)$giaKM;
        }
        return (int)($product['Gia'] ?? 0);
    }

    public function getWithOffset(int $offset, int $limit): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)
                 ORDER BY sp.NgayTao DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        return $this->layTatCa($sql);
    }

    public function getCount(): int {
        $sql = "SELECT sp.* FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.TrangThai = 1 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        return $this->demDong($sql);
    }

    public function getFilteredCount(array $filters): int {
        $sql = "SELECT sp.* FROM SANPHAM sp
                LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                WHERE sp.TrangThai = 1 
                  AND (dm.TrangThai IS NULL OR dm.TrangThai = 1) 
                  AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                  AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        
        $params = [];
        
        if (!empty($filters['categories'])) {
            $ids = array_map('intval', $filters['categories']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " AND sp.MaDanhMuc IN ($placeholders)";
            $params = array_merge($params, $ids);
        }
        
        if (!empty($filters['brands'])) {
            $ids = array_map('intval', $filters['brands']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " AND sp.MaThuongHieu IN ($placeholders)";
            $params = array_merge($params, $ids);
        }
        
        if (!empty($filters['gender'])) {
            $sql .= " AND (sp.GioiTinh = ? OR sp.GioiTinh = 'Unisex')";
            $params[] = $filters['gender'];
        }
        
        if (!empty($filters['sports'])) {
            $placeholders = implode(',', array_fill(0, count($filters['sports']), '?'));
            $sql .= " AND sp.TheThao IN ($placeholders)";
            $params = array_merge($params, $filters['sports']);
        }
        
        return $this->demDong($sql, $params);
    }

    public function getFiltered(array $filters, int $offset, int $limit, string $sort = 'default'): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1) 
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        
        $params = [];
        
        if (!empty($filters['categories'])) {
            $ids = array_map('intval', $filters['categories']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " AND sp.MaDanhMuc IN ($placeholders)";
            $params = array_merge($params, $ids);
        }
        
        if (!empty($filters['brands'])) {
            $ids = array_map('intval', $filters['brands']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql .= " AND sp.MaThuongHieu IN ($placeholders)";
            $params = array_merge($params, $ids);
        }
        
        if (!empty($filters['gender'])) {
            $sql .= " AND (sp.GioiTinh = ? OR sp.GioiTinh = 'Unisex')";
            $params[] = $filters['gender'];
        }
        
        if (!empty($filters['sports'])) {
            $placeholders = implode(',', array_fill(0, count($filters['sports']), '?'));
            $sql .= " AND sp.TheThao IN ($placeholders)";
            $params = array_merge($params, $filters['sports']);
        }
        
        // Sắp xếp
        if ($sort === 'price_asc') {
            $sql .= " ORDER BY COALESCE(NULLIF(sp.GiaKhuyenMai, 0), sp.Gia) ASC";
        } elseif ($sort === 'price_desc') {
            $sql .= " ORDER BY COALESCE(NULLIF(sp.GiaKhuyenMai, 0), sp.Gia) DESC";
        } else {
            $sql .= " ORDER BY sp.NgayTao DESC";
        }
        
        // Phân trang
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        
        return $this->layTatCa($sql, $params);
    }

    /** Tim kiem san pham */
    public function search(string $keyword): array {
        $sql  = "SELECT sp.*, CASE WHEN LOWER(dm.TenDM) = 'ao' THEN 'Áo' WHEN LOWER(dm.TenDM) = 'quan' THEN 'Quần' WHEN LOWER(dm.TenDM) = 'giay' THEN 'Giày' WHEN LOWER(dm.TenDM) = 'phu kien' THEN 'Phụ kiện' ELSE dm.TenDM END AS category, th.TenThuongHieu AS brand 
                 FROM SANPHAM sp
                 LEFT JOIN DANHMUCSP dm ON sp.MaDanhMuc = dm.MaDanhMuc
                 LEFT JOIN THUONGHIEU th ON sp.MaThuongHieu = th.MaThuongHieu
                 LEFT JOIN THETHAO tt ON sp.TheThao = tt.TenSport
                 WHERE (sp.TenSP LIKE ? OR sp.MoTaSP LIKE ?) 
                   AND sp.TrangThai = 1 
                   AND (dm.TrangThai IS NULL OR dm.TrangThai = 1)
                   AND (th.TrangThai IS NULL OR th.TrangThai = 1)
                   AND (tt.TrangThai IS NULL OR tt.TrangThai = 1)";
        $like = '%' . $keyword . '%';
        return $this->layTatCa($sql, [$like, $like]);
    }

    /** Them san pham moi */
    public function create(array $data): int {
        $sql = "INSERT INTO SANPHAM
                    (TenSP, DuongDan, MaDanhMuc, Gia, GiaKhuyenMai, AnhChinh, MoTaSP, MaThuongHieu, TheThao, GioiTinh, KichCo, TrangThai, NgayTao)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        return (int) $this->themVaLayId($sql, [
            $data['TenSP'],
            $data['DuongDan'] ?? '',
            $data['MaDanhMuc'] ?? null,
            $data['Gia'],
            $data['GiaKhuyenMai'] ?? null,
            $data['AnhChinh'] ?? '',
            $data['MoTaSP'] ?? '',
            $data['MaThuongHieu'] ?? null,
            $data['TheThao'] ?? '',
            $data['GioiTinh'] ?? '',
            $data['KichCo'] ?? '',
            $data['TrangThai'] ?? 1,
        ]);
    }

    /** Cap nhat san pham */
    public function update(int $id, array $data): bool {
        $sql = "UPDATE SANPHAM
                SET TenSP=?, DuongDan=?, MaDanhMuc=?, Gia=?, GiaKhuyenMai=?, AnhChinh=?, MoTaSP=?, MaThuongHieu=?, TheThao=?, GioiTinh=?, KichCo=?, TrangThai=?
                WHERE MaSP=?";
        return $this->thucThi($sql, [
            $data['TenSP'],
            $data['DuongDan'] ?? '',
            $data['MaDanhMuc'] ?? null,
            $data['Gia'],
            $data['GiaKhuyenMai'] ?? null,
            $data['AnhChinh'] ?? '',
            $data['MoTaSP'] ?? '',
            $data['MaThuongHieu'] ?? null,
            $data['TheThao'] ?? '',
            $data['GioiTinh'] ?? '',
            $data['KichCo'] ?? '',
            $data['TrangThai'] ?? 1,
            $id,
        ]);
    }

    public function delete(int $id): bool {
        $sql = "DELETE FROM SANPHAM WHERE MaSP = ?";
        return $this->thucThi($sql, [$id]);
    }

    /** Lay danh sach kich co va so luong cua san pham */
    public function getSizes(int $productId): array {
        $sql  = "SELECT * FROM QUANLYSP WHERE MaSP = ? ORDER BY KichCo ASC";
        return $this->layTatCa($sql, [$productId]);
    }

    /** Giam so luong ton kho cua san pham theo kich co */
    public function reduceStock(int $id, int $quantity, string $size = ''): bool {
        if (!empty($size)) {
            $sqlSize = "UPDATE QUANLYSP SET SoLuong = GREATEST(0, SoLuong - ?) WHERE MaSP = ? AND KichCo = ?";
            $this->thucThi($sqlSize, [$quantity, $id, $size]);
            return true;
        } else {
            // Fallback: giam tren tat ca kich co (lay kich co dau tien)
            $sqlFirst = "SELECT KichCo, SoLuong FROM QUANLYSP WHERE MaSP = ? ORDER BY KichCo ASC LIMIT 1";
            $first    = $this->layMot($sqlFirst, [$id]);
            if ($first) {
                $sqlSize = "UPDATE QUANLYSP SET SoLuong = GREATEST(0, SoLuong - ?) WHERE MaSP = ? AND KichCo = ?";
                $this->thucThi($sqlSize, [$quantity, $id, $first['KichCo']]);
            }
            return true;
        }
    }

    /** Kiem tra san pham con hang khong */
    public static function hasStock(int $productId): bool {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(SoLuong), 0) FROM QUANLYSP WHERE MaSP = ?");
        $stmt->execute([$productId]);
        return (int)$stmt->fetchColumn() > 0;
    }
}