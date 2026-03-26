-- =====================================================
-- 15. Bảng recruitment_related_links
-- =====================================================
CREATE TABLE IF NOT EXISTS recruitment_related_links
(
    id                INT PRIMARY KEY AUTO_INCREMENT,
    recruitment_id    INT                                              NOT NULL,
    related_type      ENUM ('news', 'recruitment', 'link', 'category') NOT NULL,
    related_id        INT                                              NOT NULL,
    title             VARCHAR(255),
    url               VARCHAR(500),
    link_order        INT                                                              DEFAULT 0,
    relationship_type ENUM ('related', 'similar_job', 'same_company', 'same_location') DEFAULT 'related',
    created_at        TIMESTAMP                                                        DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recruitment_id) REFERENCES recruitments (id) ON DELETE CASCADE,
    UNIQUE KEY unique_recruitment_related (recruitment_id, related_type, related_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index
CREATE INDEX idx_recruitment_related ON recruitment_related_links (recruitment_id);
CREATE INDEX idx_rec_related_type ON recruitment_related_links (related_type, related_id);