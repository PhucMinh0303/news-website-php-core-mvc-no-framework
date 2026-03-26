-- =====================================================
-- 2. Bảng authors
-- =====================================================
CREATE TABLE IF NOT EXISTS authors
(
    id                 INT PRIMARY KEY AUTO_INCREMENT,
    username           VARCHAR(50) UNIQUE  NOT NULL,
    password_hash      VARCHAR(255)        NOT NULL,
    full_name          VARCHAR(100)        NOT NULL,
    email              VARCHAR(100) UNIQUE NOT NULL,
    avatar             VARCHAR(255),
    bio                TEXT,
    position           VARCHAR(100),
    phone              VARCHAR(20),
    address            TEXT,
    role               ENUM ('admin', 'editor', 'author', 'recruiter') DEFAULT 'author',
    status             ENUM ('active', 'inactive', 'suspended')        DEFAULT 'active',
    last_login         TIMESTAMP           NULL,
    login_attempts     INT                                             DEFAULT 0,
    reset_token        VARCHAR(100),
    reset_token_expiry TIMESTAMP           NULL,
    created_at         TIMESTAMP                                       DEFAULT CURRENT_TIMESTAMP,
    updated_at         TIMESTAMP                                       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu cho authors
INSERT INTO authors (username, password_hash, full_name, email, role)
VALUES ('nguyenvana', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A',
        'nguyenvana@example.com', 'admin'),
       ('tranthib', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B',
        'tranthib@example.com', 'editor'),
       ('phamvanc', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Văn C',
        'phamvanc@example.com', 'author'),
       ('hoangthid', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Thị D',
        'hoangthid@example.com', 'recruiter');

-- Tạo index cho bảng authors
CREATE INDEX idx_authors_username ON authors (username);
CREATE INDEX idx_authors_email ON authors (email);
CREATE INDEX idx_authors_role ON authors (role);
CREATE INDEX idx_authors_status ON authors (status);