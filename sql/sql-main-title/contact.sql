-- Bảng thư liên hệ dùng chung với ContactModel và màn quản trị.
CREATE TABLE IF NOT EXISTS contacts
(
    id               INT PRIMARY KEY AUTO_INCREMENT,
    customer_name    VARCHAR(100) NOT NULL,
    phone            VARCHAR(20)  NOT NULL,
    email            VARCHAR(100) NULL,
    content          TEXT         NOT NULL,
    contact_type     ENUM ('general', 'support', 'feedback', 'complaint', 'recruitment', 'partnership') DEFAULT 'general',
    category_id      INT NULL,
    source           ENUM ('website', 'mobile', 'email', 'phone', 'social') DEFAULT 'website',
    ip_address       VARCHAR(45) NULL,
    user_agent       TEXT NULL,
    page_url         VARCHAR(500) NULL,
    referrer_url     VARCHAR(500) NULL,
    status           ENUM ('new', 'read', 'replied', 'processing', 'resolved', 'spam', 'archived') DEFAULT 'new',
    priority         ENUM ('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    assigned_to      INT NULL,
    response_content TEXT NULL,
    response_by      INT NULL,
    response_at      TIMESTAMP NULL,
    customer_id      INT NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_contacts_status (status),
    INDEX idx_contacts_created_at (created_at),
    INDEX idx_contacts_email (email),
    INDEX idx_contacts_phone (phone)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Upgrade existing contacts tables; CREATE TABLE IF NOT EXISTS does not alter them.
ALTER TABLE contacts
    ADD COLUMN IF NOT EXISTS contact_type ENUM ('general', 'support', 'feedback', 'complaint', 'recruitment', 'partnership') DEFAULT 'general',
    ADD COLUMN IF NOT EXISTS category_id INT NULL,
    ADD COLUMN IF NOT EXISTS source ENUM ('website', 'mobile', 'email', 'phone', 'social') DEFAULT 'website',
    ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45) NULL,
    ADD COLUMN IF NOT EXISTS user_agent TEXT NULL,
    ADD COLUMN IF NOT EXISTS page_url VARCHAR(500) NULL,
    ADD COLUMN IF NOT EXISTS referrer_url VARCHAR(500) NULL,
    ADD COLUMN IF NOT EXISTS status ENUM ('new', 'read', 'replied', 'processing', 'resolved', 'spam', 'archived') DEFAULT 'new',
    ADD COLUMN IF NOT EXISTS priority ENUM ('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    ADD COLUMN IF NOT EXISTS assigned_to INT NULL,
    ADD COLUMN IF NOT EXISTS response_content TEXT NULL,
    ADD COLUMN IF NOT EXISTS response_by INT NULL,
    ADD COLUMN IF NOT EXISTS response_at TIMESTAMP NULL,
    ADD COLUMN IF NOT EXISTS customer_id INT NULL,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

  ALTER TABLE contacts
    MODIFY COLUMN status ENUM ('new', 'read', 'replied', 'processing', 'resolved', 'spam', 'archived') DEFAULT 'new';