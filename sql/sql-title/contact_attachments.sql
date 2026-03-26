-- =====================================================
-- 10. Bảng contact_attachments
-- =====================================================
CREATE TABLE IF NOT EXISTS contact_attachments
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    contact_id  INT          NOT NULL,
    file_name   VARCHAR(255) NOT NULL,
    file_path   VARCHAR(500) NOT NULL,
    file_type   VARCHAR(100),
    file_size   INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index cho bảng contact_attachments
CREATE INDEX idx_attachments_contact ON contact_attachments (contact_id);