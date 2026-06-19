<?php

/**
 * Database Configuration & Connection
 * Không dùng define, không dùng hằng số
 */
// Cấu hình chạy Local (XAMPP)
$DB_HOST    = 'localhost';
$DB_PORT    = 3306;
$DB_NAME    = 'vabshop2026_db';
$DB_USER    = 'root';
$DB_PASS    = ''; 
$DB_CHARSET = 'utf8mb4';

/*
// Cấu hình chạy Online (Alwaysdata)
$DB_HOST    = 'mysql-vabshop2026.alwaysdata.net';
$DB_PORT    = 3306;
$DB_NAME    = 'vabshop2026_db';
$DB_USER    = 'vabshop2026_user';
$DB_PASS    = 'Vabshop2026@';
$DB_CHARSET = 'utf8mb4';
*/

/**
 * Get PDO Database Connection
 */
function getConnection(): PDO
{
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHARSET;
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . $DB_HOST . ";port=" . $DB_PORT . ";dbname=" . $DB_NAME . ";charset=" . $DB_CHARSET;
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            // Thiết lập múi giờ GMT+7 cho kết nối MySQL
            $pdo->exec("SET time_zone = '+07:00';");
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }
    return $pdo;
}
