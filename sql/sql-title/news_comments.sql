-- =====================================================
-- 22. Bảng news_comments
-- =====================================================
CREATE TABLE IF NOT EXISTS news_comments
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    news_id        INT          NOT NULL,
    parent_id      INT       DEFAULT NULL,
    author_name    VARCHAR(100) NOT NULL,
    author_email   VARCHAR(100),
    author_website VARCHAR(255),
    author_ip      VARCHAR(45),
    content        TEXT         NOT NULL,
    is_approved    BOOLEAN   DEFAULT FALSE,
    like_count     INT       DEFAULT 0,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES news_comments (id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index
CREATE INDEX idx_comments_news ON news_comments (news_id);
CREATE INDEX idx_comments_approved ON news_comments (is_approved);
CREATE INDEX idx_comments_created ON news_comments (created_at);