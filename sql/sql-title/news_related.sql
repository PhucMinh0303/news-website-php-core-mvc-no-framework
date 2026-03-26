-- =====================================================
-- 24. Bảng news_related
-- =====================================================
CREATE TABLE IF NOT EXISTS news_related
(
    id                INT PRIMARY KEY AUTO_INCREMENT,
    news_id           INT NOT NULL,
    related_news_id   INT NOT NULL,
    relationship_type ENUM ('related', 'similar', 'series', 'recommended') DEFAULT 'related',
    sort_order        INT                                                  DEFAULT 0,
    created_at        TIMESTAMP                                            DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE,
    FOREIGN KEY (related_news_id) REFERENCES news (id) ON DELETE CASCADE,
    UNIQUE KEY unique_related (news_id, related_news_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE INDEX idx_related_news ON news_related (news_id);