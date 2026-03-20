<?php

class Database {
    private $conn;

    public function __construct() {
        $config = require __DIR__ . '/../config/database.php';

        try {
            $this->conn = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}",
                $config['username'],
                $config['password']
            );

            // bật lỗi PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "✅ Kết nối thành công!";

        } catch (PDOException $e) {
            die("❌ Kết nối thất bại: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}