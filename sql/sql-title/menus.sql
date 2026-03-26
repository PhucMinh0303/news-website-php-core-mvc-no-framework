-- =====================================================
-- 16. Bảng menus
-- =====================================================
CREATE TABLE IF NOT EXISTS menus
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100)                                                  NOT NULL,
    slug        VARCHAR(100) UNIQUE                                           NOT NULL,
    location    ENUM ('header', 'footer', 'sidebar', 'mobile', 'quick_links') NOT NULL,
    description TEXT,
    is_active   BOOLEAN   DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Thêm menu mẫu
INSERT INTO menus (name, slug, location, description)
VALUES ('Menu chính', 'main-menu', 'header', 'Menu điều hướng chính'),
       ('Menu footer', 'footer-menu', 'footer', 'Menu chân trang'),
       ('Liên kết nhanh', 'quick-links', 'sidebar', 'Liên kết nhanh trong sidebar');