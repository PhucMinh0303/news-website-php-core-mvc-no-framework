-- =====================================================
-- 6. Bảng permissions
-- =====================================================
CREATE TABLE IF NOT EXISTS permissions
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255)
);

-- Thêm dữ liệu mẫu cho permissions
INSERT INTO permissions (name, description)
VALUES ('news_create', 'Tạo tin tức mới'),
       ('news_edit', 'Chỉnh sửa tin tức'),
       ('news_delete', 'Xóa tin tức'),
       ('news_publish', 'Xuất bản tin tức'),
       ('recruitment_create', 'Tạo tin tuyển dụng'),
       ('recruitment_edit', 'Chỉnh sửa tin tuyển dụng'),
       ('recruitment_delete', 'Xóa tin tuyển dụng'),
       ('user_manage', 'Quản lý người dùng'),
       ('category_manage', 'Quản lý danh mục');
