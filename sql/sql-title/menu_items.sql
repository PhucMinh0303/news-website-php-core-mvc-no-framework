-- =====================================================
-- 17. Bảng menu_items
-- =====================================================
CREATE TABLE IF NOT EXISTS menu_items
(
    id           INT PRIMARY KEY AUTO_INCREMENT,
    menu_id      INT NOT NULL,
    parent_id    INT       DEFAULT NULL,
    link_id      INT,
    custom_title VARCHAR(255),
    custom_url   VARCHAR(500),
    icon_class   VARCHAR(100),
    css_class    VARCHAR(100),
    sort_order   INT       DEFAULT 0,
    is_active    BOOLEAN   DEFAULT TRUE,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_id) REFERENCES menus (id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES menu_items (id) ON DELETE CASCADE,
    FOREIGN KEY (link_id) REFERENCES links (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;
-- Thêm menu items
INSERT INTO menu_items (menu_id, link_id, sort_order)
VALUES (1, 1, 1), -- Trang chủ
       (1, 2, 2), -- Tin tức
       (1, 3, 3), -- Tuyển dụng
       (1, 5, 4), -- Giới thiệu
       (1, 4, 5), -- Liên hệ
       (2, 6, 1), -- Facebook footer
       (2, 7, 2);
-- YouTube footer

-- Tạo index
CREATE INDEX idx_menu_items_menu ON menu_items (menu_id);
CREATE INDEX idx_menu_items_parent ON menu_items (parent_id);
CREATE INDEX idx_menu_items_order ON menu_items (sort_order);