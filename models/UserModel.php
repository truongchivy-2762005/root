<?php
/**
 * UserModel - Tuong tac voi bang KHACHHANG
 */

if (!class_exists('xl_database')) {
    require './models/xl_database.php';
}

class UserModel extends xl_database {

    public function getAll(): array {
        $sql = "SELECT * FROM KHACHHANG ORDER BY NgayTao DESC";
        return $this->layTatCa($sql);
    }

    public function getById(int $maKH): ?array {
        $sql = "SELECT * FROM KHACHHANG WHERE MaKH = ?";
        return $this->layMot($sql, [$maKH]);
    }

    public function getByEmail(string $email): ?array {
        $sql = "SELECT * FROM KHACHHANG WHERE email = ?";
        return $this->layMot($sql, [$email]);
    }

    public function create(array $duLieu): int {
        $matKhau = password_hash($duLieu['MatKhauKH'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO KHACHHANG (HoTenKH, email, MatKhauKH, SDT, NgayTao) VALUES (?, ?, ?, ?, NOW())";
        return (int) $this->themVaLayId($sql, [
            $duLieu['HoTenKH'],
            $duLieu['email'],
            $matKhau,
            $duLieu['SDT'] ?? '',
        ]);
    }

    public function authenticate(string $email, string $matKhau): ?array {
        $nguoiDung = $this->getByEmail($email);
        if ($nguoiDung && password_verify($matKhau, $nguoiDung['MatKhauKH'])) {
            return $nguoiDung;
        }
        return null;
    }

    public function update(int $maKH, array $duLieu): bool {
        $sql = "UPDATE KHACHHANG SET HoTenKH = ?, email = ?, SDT = ? WHERE MaKH = ?";
        return $this->thucThi($sql, [
            $duLieu['HoTenKH'],
            $duLieu['email'],
            $duLieu['SDT'] ?? '',
            $maKH,
        ]);
    }

    public function delete(int $maKH): bool {
        $sql = "DELETE FROM KHACHHANG WHERE MaKH = ?";
        return $this->thucThi($sql, [$maKH]);
    }
}