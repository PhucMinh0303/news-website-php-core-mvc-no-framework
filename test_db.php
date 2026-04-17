<?php

require_once 'app/config/database.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "Kết nối thành công  - PDO Connected";

} catch (PDOException $e) {
    echo "Kết nối thất bại - Vui lòng thử lại: " . $e->getMessage();
}