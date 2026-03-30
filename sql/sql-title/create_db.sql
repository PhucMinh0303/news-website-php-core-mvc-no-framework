-- Tạo database
CREATE DATABASE IF NOT EXISTS quanly_tintuc
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

-- Tạo user
CREATE USER IF NOT EXISTS 'ten_user'@'localhost' IDENTIFIED BY 'mat_khau';

-- Cấp quyền cho database
GRANT ALL PRIVILEGES ON ten_database.* TO 'ten_user'@'localhost';

-- Áp dụng thay đổi
FLUSH PRIVILEGES;

-- Ví dụ
CREATE USER IF NOT EXISTS 'capitalam'@'localhost' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON quanly_tintuc.* TO 'capitalam'@'localhost';
FLUSH PRIVILEGES;


-- Sửa user (thay đổi username và password theo nhu cầu)
CREATE USER IF NOT EXISTS 'capitalam2'@'localhost' IDENTIFIED BY '123456789';
GRANT ALL PRIVILEGES ON quanly_tintuc.* TO 'capitalam2'@'localhost';
FLUSH PRIVILEGES;

-- Sử dụng database
USE quanly_tintuc;