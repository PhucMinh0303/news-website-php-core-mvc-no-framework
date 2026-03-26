-- =====================================================
-- 18. Bảng breadcrumb_paths
-- =====================================================
CREATE TABLE IF NOT EXISTS breadcrumb_paths
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    entity_type ENUM ('news', 'recruitment', 'category', 'page') NOT NULL,
    entity_id   INT                                              NOT NULL,
    path_json   JSON                                             NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_breadcrumb (entity_type, entity_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE INDEX idx_breadcrumb_entity ON breadcrumb_paths (entity_type, entity_id);