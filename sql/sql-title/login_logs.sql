-- =====================================================
-- 5. Bảng login_logs
-- =====================================================
CREATE TABLE IF NOT EXISTS login_logs
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    author_id      INT,
    username       VARCHAR(50),
    ip_address     VARCHAR(45),
    user_agent     TEXT,
    login_time     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success        BOOLEAN   DEFAULT FALSE,
    failure_reason VARCHAR(255),
    FOREIGN KEY (author_id) REFERENCES authors (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index cho bảng login_logs
CREATE INDEX idx_login_logs_author ON login_logs (author_id);
CREATE INDEX idx_login_logs_time ON login_logs (login_time);
CREATE INDEX idx_login_logs_success ON login_logs (success);
