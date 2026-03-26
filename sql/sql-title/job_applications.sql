-- Tạo bảng ứng tuyển (job_applications)
CREATE TABLE IF NOT EXISTS job_applications
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    recruitment_id INT          NOT NULL,
    full_name      VARCHAR(100) NOT NULL,
    phone          VARCHAR(20)  NOT NULL,
    email          VARCHAR(100) NOT NULL,
    content        TEXT,
    cv_file        VARCHAR(255),
    status         ENUM ('pending', 'reviewed', 'interviewed', 'accepted', 'rejected') DEFAULT 'pending',
    notes          TEXT,
    created_at     TIMESTAMP                                                           DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP                                                           DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (recruitment_id) REFERENCES recruitments (id) ON DELETE CASCADE,
    INDEX idx_recruitment_id (recruitment_id),
    INDEX idx_status (status),
    INDEX idx_email (email)
    ) ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

-- Tạo bảng lưu lượt xem chi tiết (tùy chọn)
CREATE TABLE IF NOT EXISTS recruitment_views
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    recruitment_id INT NOT NULL,
    ip_address     VARCHAR(45),
    user_agent     TEXT,
    viewed_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recruitment_id) REFERENCES recruitments (id) ON DELETE CASCADE,
    INDEX idx_recruitment_id (recruitment_id),
    INDEX idx_viewed_at (viewed_at)
    ) ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;