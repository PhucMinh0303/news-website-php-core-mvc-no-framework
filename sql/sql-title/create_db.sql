-- Tạo database
CREATE DATABASE IF NOT EXISTS quanly_tintuc
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

-- Tạo user (thay đổi username và password theo nhu cầu)
CREATE USER IF NOT EXISTS 'capitalam2'@'localhost' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON quanly_tintuc.* TO 'capitalam2'@'localhost';
FLUSH PRIVILEGES;

-- Sử dụng database
USE quanly_tintuc;