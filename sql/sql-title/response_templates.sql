-- =====================================================
-- 12. Bảng response_templates
-- =====================================================
CREATE TABLE IF NOT EXISTS response_templates
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    title       VARCHAR(255)        NOT NULL,
    slug        VARCHAR(100) UNIQUE NOT NULL,
    subject     VARCHAR(255)        NOT NULL,
    content     TEXT                NOT NULL,
    category_id INT,
    is_active   BOOLEAN   DEFAULT TRUE,
    created_by  INT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES contact_categories (id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES authors (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu cho response_templates
INSERT INTO response_templates (title, slug, subject, content, category_id)
VALUES ('Xác nhận tiếp nhận', 'xac-nhan-tiep-nhan', 'Đã nhận được liên hệ của bạn',
        'Kính gửi {customer_name},\n\nChúng tôi đã nhận được thông tin liên hệ của bạn và sẽ phản hồi trong thời gian sớm nhất.\n\nTrân trọng,\nĐội ngũ hỗ trợ',
        1),
       ('Phản hồi tuyển dụng', 'phan-hoi-tuyen-dung', 'Thông tin tuyển dụng',
        'Kính gửi {customer_name},\n\nCảm ơn bạn đã quan tâm đến vị trí tuyển dụng. Chúng tôi sẽ xem xét hồ sơ của bạn và liên hệ trong thời gian tới.\n\nTrân trọng,\nPhòng Nhân sự',
        3);