-- =====================================================
-- 21. Bảng news_tag_relations
-- =====================================================
CREATE TABLE IF NOT EXISTS news_tag_relations
(
    news_id    INT NOT NULL,
    tag_id     INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (news_id, tag_id),
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES news_tags (id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;