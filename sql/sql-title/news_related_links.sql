-- =====================================================
-- 14. Bảng news_related_links
-- =====================================================
CREATE TABLE IF NOT EXISTS news_related_links
(
    id                INT PRIMARY KEY AUTO_INCREMENT,
    news_id           INT                                              NOT NULL,
    related_type      ENUM ('news', 'recruitment', 'link', 'category') NOT NULL,
    related_id        INT                                              NOT NULL,
    title             VARCHAR(255),
    url               VARCHAR(500),
    link_order        INT                                                  DEFAULT 0,
    relationship_type ENUM ('related', 'recommended', 'similar', 'series') DEFAULT 'related',
    created_at        TIMESTAMP                                            DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE,
    UNIQUE KEY unique_news_related (news_id, related_type, related_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index
CREATE INDEX idx_news_related ON news_related_links (news_id);
CREATE INDEX idx_related_type ON news_related_links (related_type, related_id);