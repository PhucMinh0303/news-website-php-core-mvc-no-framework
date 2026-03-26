-- =====================================================
-- 9. Bảng contact_categories
-- =====================================================
CREATE TABLE IF NOT EXISTS contact_categories
(
    id                 INT PRIMARY KEY AUTO_INCREMENT,
    name               VARCHAR(100)        NOT NULL,
    slug               VARCHAR(100) UNIQUE NOT NULL,
    description        TEXT,
    default_assignee   INT,
    response_template  TEXT,
    auto_reply_subject VARCHAR(255),
    auto_reply_content TEXT,
    is_active          BOOLEAN   DEFAULT TRUE,
    created_at         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (default_assignee) REFERENCES authors (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu cho contact_categories
INSERT INTO contact_categories (name, slug, description)
VALUES ('Hỗ trợ kỹ thuật', 'ho-tro-ky-thuat', 'Các vấn đề về kỹ thuật website'),
       ('Thắc mắc sản phẩm', 'thac-mac-san-pham', 'Câu hỏi về sản phẩm/dịch vụ'),
       ('Tuyển dụng', 'tuyen-dung', 'Liên hệ về tuyển dụng'),
       ('Hợp tác', 'hop-tac', 'Đề xuất hợp tác kinh doanh'),
       ('Phản hồi', 'phan-hoi', 'Ý kiến phản hồi từ khách hàng'),
       ('Khiếu nại', 'khieu-nai', 'Khiếu nại về dịch vụ'),
       ('Khác', 'khac', 'Các liên hệ khác');