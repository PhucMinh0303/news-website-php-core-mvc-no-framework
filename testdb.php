<?php
require_once __DIR__ . '/app/core/Database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    echo "✅ Kết nối database thành công!<br>";
    echo "Host: " . DB_HOST . "<br>";
    echo "Database: " . DB_NAME . "<br>";
    echo "User: " . DB_USER . "<br>";

    // Kiểm tra version MySQL
    $stmt = $conn->query("SELECT VERSION() as version");
    $result = $stmt->fetch();
    echo "MySQL Version: " . $result['version'];
} catch (Exception $e) {
    echo "❌ Lỗi kết nối: " . $e->getMessage();
}
