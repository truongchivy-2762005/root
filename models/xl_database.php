<?php

class xl_database
{
    protected $conn;

    public function __construct()
    {
        $this->conn = getConnection();
    }

    /**
     * Dùng cho SELECT nhiều dòng
     */
    public function layTatCa($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Lỗi truy vấn layTatCa: ' . $e->getMessage());
        }
    }

    /**
     * Dùng cho SELECT một dòng
     */
    public function layMot($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            die('Lỗi truy vấn layMot: ' . $e->getMessage());
        }
    }

    /**
     * Dùng cho INSERT, UPDATE, DELETE
     */
    public function thucThi($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            die('Lỗi truy vấn thucThi: ' . $e->getMessage());
        }
    }

    /**
     * Dùng khi thêm dữ liệu và lấy ID vừa thêm
     */
    public function themVaLayId($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            die('Lỗi truy vấn themVaLayId: ' . $e->getMessage());
        }
    }

    /**
     * Dùng để đếm số dòng kết quả từ câu truy vấn
     */
    public function demDong($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            die('Lỗi truy vấn demDong: ' . $e->getMessage());
        }
    }
}
