-- =====================================================
-- 1. Bảng categories
-- =====================================================
CREATE TABLE IF NOT EXISTS categories
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100)        NOT NULL,
    slug        VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu cho categories
INSERT INTO categories (name, slug, description)
VALUES ('Thời sự', 'thoi-su', 'Tin tức thời sự trong nước và quốc tế'),
       ('Kinh tế', 'kinh-te', 'Tin tức kinh tế, tài chính'),
       ('Công nghệ', 'cong-nghe', 'Tin công nghệ, khoa học kỹ thuật'),
       ('Bất động sản', 'bat-dong-san', 'Tin bất động sản, khoa học kỹ thuật'),
       ('Doanh nghiệp', 'doanh-nghiep', 'Tin doanh nghiệp, doanh nghiệp'),
       ('Tài chính quốc tế', 'tai-chinh-quoc-te', 'Tin tài chính quốc tế, tài chính'),
       ('Vĩ mô', 'vi-mo', 'Tin vĩ mô, vĩ mô'),
       ('Chứng khoán', 'chung-khoan', 'Tin chứng khoán, chứng khoán'),
       ('Ngân hàng', 'ngan-hang', 'Tin Ngân hàng, Ngân hàng');
