-- =====================================================
-- 7. Bảng role_permissions
-- =====================================================
CREATE TABLE IF NOT EXISTS role_permissions
(
    role          VARCHAR(50),
    permission_id INT,
    PRIMARY KEY (role, permission_id),
    FOREIGN KEY (permission_id) REFERENCES permissions (id) ON DELETE CASCADE
    );

-- Gán quyền cho các vai trò
INSERT INTO role_permissions (role, permission_id)
VALUES ('admin', 1),
       ('admin', 2),
       ('admin', 3),
       ('admin', 4),
       ('admin', 5),
       ('admin', 6),
       ('admin', 7),
       ('admin', 8),
       ('admin', 9),
       ('editor', 1),
       ('editor', 2),
       ('editor', 4),
       ('author', 1),
       ('author', 2),
       ('recruiter', 5),
       ('recruiter', 6);