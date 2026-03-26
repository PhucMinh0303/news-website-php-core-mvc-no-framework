-- =====================================================
-- 13. Bảng links
-- =====================================================
CREATE TABLE IF NOT EXISTS links
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    title       VARCHAR(255)        NOT NULL,
    url         VARCHAR(500)        NOT NULL,
    slug        VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    link_type   ENUM ('internal', 'external', 'anchor', 'file') DEFAULT 'internal',
    target      ENUM ('_self', '_blank', '_parent', '_top')     DEFAULT '_blank',
    rel         VARCHAR(100)                                    DEFAULT 'noopener noreferrer',
    icon_class  VARCHAR(100),
    image_url   VARCHAR(500),
    sort_order  INT                                             DEFAULT 0,
    parent_id   INT                                             DEFAULT NULL,
    is_active   BOOLEAN                                         DEFAULT TRUE,
    click_count INT                                             DEFAULT 0,
    created_by  INT,
    created_at  TIMESTAMP                                       DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP                                       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES links (id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES authors (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index cho bảng links
CREATE INDEX idx_links_slug ON links (slug);
CREATE INDEX idx_links_type ON links (link_type);
CREATE INDEX idx_links_parent ON links (parent_id);
CREATE INDEX idx_links_active ON links (is_active);
CREATE INDEX idx_links_sort ON links (sort_order);

-- Thêm link mẫu
INSERT INTO links (title, url, slug, link_type, target)
VALUES ('Trang chủ', '/', 'home', 'internal', '_self'),
       ('Tin tức', '/tin-tuc', 'news', 'internal', '_self'),
       ('Tuyển dụng', '/tuyen-dung', 'recruitment', 'internal', '_self'),
       ('Liên hệ', '/lien-he', 'contact', 'internal', '_self'),
       ('Giới thiệu', '/gioi-thieu', 'about', 'internal', '_self'),
       ('Facebook', 'https://facebook.com/yourpage', 'facebook', 'external', '_blank'),
       ('YouTube', 'https://youtube.com/yourchannel', 'youtube', 'external', '_blank');