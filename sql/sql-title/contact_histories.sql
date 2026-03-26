-- =====================================================
-- 11. Bảng contact_histories
-- =====================================================
CREATE TABLE IF NOT EXISTS contact_histories
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    contact_id INT                                                                                                 NOT NULL,
    author_id  INT,
    action     ENUM ('created', 'read', 'assigned', 'replied', 'status_changed', 'note_added', 'priority_changed') NOT NULL,
    old_value  TEXT,
    new_value  TEXT,
    note       TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES authors (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu cho contact_histories
INSERT INTO contact_histories (contact_id, action, note)
VALUES (1, 'note_added', 'Đã liên hệ khách hàng qua điện thoại, chờ phản hồi'),
       (3, 'assigned', 'Phân công cho đội kỹ thuật xử lý'),
       (5, 'replied', 'Đã gửi email phản hồi và xin lỗi khách hàng');

-- Tạo index cho bảng contact_histories
CREATE INDEX idx_histories_contact ON contact_histories (contact_id);
CREATE INDEX idx_histories_created_at ON contact_histories (created_at);