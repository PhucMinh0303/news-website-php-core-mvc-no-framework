-- =====================================================
-- 23. Bảng news_views_stats
-- =====================================================
CREATE TABLE IF NOT EXISTS news_views_stats
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    news_id    INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer   VARCHAR(500),
    viewed_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index
CREATE INDEX idx_views_news ON news_views_stats (news_id);
CREATE INDEX idx_views_ip ON news_views_stats (ip_address);
CREATE INDEX idx_views_date ON news_views_stats (viewed_at);