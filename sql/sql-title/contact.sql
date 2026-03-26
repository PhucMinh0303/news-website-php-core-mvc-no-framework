-- =====================================================
-- 8. Bảng contacts
-- =====================================================
CREATE TABLE IF NOT EXISTS contacts
(
    id               INT PRIMARY KEY AUTO_INCREMENT,
    customer_name    VARCHAR(100) NOT NULL,
    phone            VARCHAR(20)  NOT NULL,
    email            VARCHAR(100),
    content          TEXT         NOT NULL,
    contact_type     ENUM ('general', 'support', 'feedback', 'complaint', 'recruitment', 'partnership') DEFAULT 'general',
    category_id      INT,
    source           ENUM ('website', 'mobile', 'email', 'phone', 'social')                             DEFAULT 'website',
    ip_address       VARCHAR(45),
    user_agent       TEXT,
    page_url         VARCHAR(500),
    referrer_url     VARCHAR(500),
    status           ENUM ('new', 'read', 'replied', 'processing', 'resolved', 'spam')                  DEFAULT 'new',
    priority         ENUM ('low', 'medium', 'high', 'urgent')                                           DEFAULT 'medium',
    assigned_to      INT,
    response_content TEXT,
    response_by      INT,
    response_at      TIMESTAMP    NULL,
    customer_id      INT,
    created_at       TIMESTAMP                                                                          DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP                                                                          DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES authors (id) ON DELETE SET NULL,
    FOREIGN KEY (response_by) REFERENCES authors (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu cho contacts
INSERT INTO contacts (customer_name, phone, email, content, contact_type, category_id, source, status, priority)
VALUES ('Nguyễn Văn A', '0909123456', 'nguyena@gmail.com', 'Tôi cần hỗ trợ về sản phẩm X', 'support', 2, 'website',
        'new', 'medium'),
       ('Trần Thị B', '0918234567', 'tranthib@yahoo.com', 'Ứng tuyển vị trí lập trình viên', 'recruitment', 3,
        'website', 'new', 'high'),
       ('Lê Văn C', '0987654321', 'levanc@gmail.com', 'Website bị lỗi không đăng nhập được', 'support', 1, 'mobile',
        'processing', 'urgent'),
       ('Phạm Thị D', '0978123456', NULL, 'Đề xuất hợp tác kinh doanh', 'partnership', 4, 'email', 'read', 'low'),
       ('Hoàng Văn E', '0967890123', 'hoange@gmail.com', 'Khiếu nại về chất lượng dịch vụ', 'complaint', 6, 'website',
        'replied', 'high');

-- Tạo index cho bảng contacts
CREATE INDEX idx_contacts_status ON contacts (status);
CREATE INDEX idx_contacts_created_at ON contacts (created_at);
CREATE INDEX idx_contacts_email ON contacts (email);
CREATE INDEX idx_contacts_phone ON contacts (phone);
CREATE INDEX idx_contacts_type ON contacts (contact_type);
CREATE INDEX idx_contacts_priority ON contacts (priority);
CREATE INDEX idx_contacts_assigned_to ON contacts (assigned_to);
