-- Tạo database
CREATE DATABASE IF NOT EXISTS quanly_tintuc
DEFAULT CHARACTER SET utf8mb4
DEFAULT COLLATE utf8mb4_unicode_ci;

-- Tạo user (thay đổi username và password theo nhu cầu)
CREATE USER 'admin_tintuc'@'localhost' IDENTIFIED BY 'YourSecurePassword123!';
GRANT ALL PRIVILEGES ON quanly_tintuc.* TO 'admin_tintuc'@'localhost';
FLUSH PRIVILEGES;

-- Sử dụng database
USE quanly_tintuc;

-- Tạo bảng categories
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng authors (phiên bản đầy đủ với đăng nhập)
CREATE TABLE authors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    avatar VARCHAR(255),
    bio TEXT,
    position VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'editor', 'author', 'recruiter') DEFAULT 'author',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    login_attempts INT DEFAULT 0,
    reset_token VARCHAR(100),
    reset_token_expiry TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng news
CREATE TABLE news (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    category_id INT,
    author_id INT,
    author VARCHAR(100) NOT NULL,
    publish_date DATE NOT NULL,
    image VARCHAR(255),
    content LONGTEXT NOT NULL,
    views INT DEFAULT 0,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng recruitments
CREATE TABLE recruitments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recruitment_title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    job_description LONGTEXT NOT NULL,
    job_requirements LONGTEXT NOT NULL,
    job_benefits LONGTEXT,
    image VARCHAR(255),
    salary_range VARCHAR(100),
    location VARCHAR(200),
    deadline DATE,
    quantity INT DEFAULT 1,
    position VARCHAR(100),
    experience VARCHAR(100),
    education VARCHAR(100),
    job_type ENUM('fulltime', 'parttime', 'contract', 'internship') DEFAULT 'fulltime',
    status ENUM('draft', 'open', 'closed', 'filled') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng login_logs
CREATE TABLE login_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    author_id INT,
    username VARCHAR(50),
    ip_address VARCHAR(45),
    user_agent TEXT,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT FALSE,
    failure_reason VARCHAR(255),
    
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng permissions
CREATE TABLE permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255)
);

-- Tạo bảng role_permissions
CREATE TABLE role_permissions (
    role VARCHAR(50),
    permission_id INT,
    PRIMARY KEY (role, permission_id)
);

-- Tạo bảng contacts
CREATE TABLE contacts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    content TEXT NOT NULL,
    contact_type ENUM('general', 'support', 'feedback', 'complaint', 'recruitment', 'partnership') DEFAULT 'general',
    category_id INT,
    source ENUM('website', 'mobile', 'email', 'phone', 'social') DEFAULT 'website',
    ip_address VARCHAR(45),
    user_agent TEXT,
    page_url VARCHAR(500),
    referrer_url VARCHAR(500),
    status ENUM('new', 'read', 'replied', 'processing', 'resolved', 'spam') DEFAULT 'new',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    assigned_to INT,
    response_content TEXT,
    response_by INT,
    response_at TIMESTAMP NULL,
    customer_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (assigned_to) REFERENCES authors(id) ON DELETE SET NULL,
    FOREIGN KEY (response_by) REFERENCES authors(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng contact_categories
CREATE TABLE contact_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    default_assignee INT,
    response_template TEXT,
    auto_reply_subject VARCHAR(255),
    auto_reply_content TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (default_assignee) REFERENCES authors(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng contact_attachments
CREATE TABLE contact_attachments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contact_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(100),
    file_size INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng contact_histories
CREATE TABLE contact_histories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    contact_id INT NOT NULL,
    author_id INT,
    action ENUM('created', 'read', 'assigned', 'replied', 'status_changed', 'note_added', 'priority_changed') NOT NULL,
    old_value TEXT,
    new_value TEXT,
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng response_templates
CREATE TABLE response_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES contact_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES authors(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo các index cho bảng news
CREATE INDEX idx_news_publish_date ON news(publish_date);
CREATE INDEX idx_news_category ON news(category_id);
CREATE INDEX idx_news_status ON news(status);

-- Tạo các index cho bảng recruitments
CREATE INDEX idx_recruitments_status ON recruitments(status);
CREATE INDEX idx_recruitments_deadline ON recruitments(deadline);
CREATE INDEX idx_recruitments_job_type ON recruitments(job_type);

-- Tạo các index cho bảng authors
CREATE INDEX idx_authors_username ON authors(username);
CREATE INDEX idx_authors_email ON authors(email);
CREATE INDEX idx_authors_role ON authors(role);
CREATE INDEX idx_authors_status ON authors(status);

-- Tạo các index cho bảng login_logs
CREATE INDEX idx_login_logs_author ON login_logs(author_id);
CREATE INDEX idx_login_logs_time ON login_logs(login_time);
CREATE INDEX idx_login_logs_success ON login_logs(success);

-- Tạo các index cho bảng contacts
CREATE INDEX idx_contacts_status ON contacts(status);
CREATE INDEX idx_contacts_created_at ON contacts(created_at DESC);
CREATE INDEX idx_contacts_email ON contacts(email);
CREATE INDEX idx_contacts_phone ON contacts(phone);
CREATE INDEX idx_contacts_type ON contacts(contact_type);
CREATE INDEX idx_contacts_priority ON contacts(priority);
CREATE INDEX idx_contacts_assigned_to ON contacts(assigned_to);

-- Tạo index cho bảng contact_attachments
CREATE INDEX idx_attachments_contact ON contact_attachments(contact_id);

-- Tạo index cho bảng contact_histories
CREATE INDEX idx_histories_contact ON contact_histories(contact_id);
CREATE INDEX idx_histories_created_at ON contact_histories(created_at DESC);

-- Thêm dữ liệu mẫu cho categories
INSERT INTO categories (name, slug, description) VALUES
('Thời sự', 'thoi-su', 'Tin tức thời sự trong nước và quốc tế'),
('Kinh tế', 'kinh-te', 'Tin tức kinh tế, tài chính'),
('Công nghệ', 'cong-nghe', 'Tin công nghệ, khoa học kỹ thuật'),
('Bất động sản', 'bat-dong-san', 'Tin bất động sản, khoa học kỹ thuật'),
('Doanh nghiệp', 'doanh-nghiep', 'Tin doanh nghiệp, doanh nghiệp');
('Tài chính quốc tế', 'tai-chinh-quoc-te', 'Tin tài chính quốc tế, tài chính');
('Vĩ mô', 'vi-mo', 'Tin vĩ mô, vĩ mô');
('Chứng khoán', 'chung-khoan', 'Tin chứng khoán, chứng khoán');
('Ngân hàng', 'ngan-hang', 'Tin Ngân hàng, Ngân hàng');



-- Thêm dữ liệu mẫu cho authors (users)
INSERT INTO authors (username, password_hash, full_name, email, role) VALUES
('nguyenvana', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', 'nguyenvana@example.com', 'admin'),
('tranthib', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị B', 'tranthib@example.com', 'editor'),
('phamvanc', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Văn C', 'phamvanc@example.com', 'author'),
('hoangthid', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Thị D', 'hoangthid@example.com', 'recruiter');

-- Thêm dữ liệu mẫu cho news
INSERT INTO news (title, slug, category_id, author, publish_date, image, content, status) 
VALUES (
    'Tin tức công nghệ mới nhất 2024',
    'tin-tuc-cong-nghe-moi-nhat-2024',
    3,
    'Nguyễn Văn A',
    '2024-01-15',
    'news-image.jpg',
    '<p>Nội dung chi tiết của tin tức công nghệ...</p>',
    'published'
);

-- Thêm dữ liệu mẫu cho recruitments
INSERT INTO recruitments (
    recruitment_title,
    slug,
    job_description,
    job_requirements,
    job_benefits,
    image,
    salary_range,
    location,
    deadline,
    position,
    status
) VALUES (
    'Tuyển dụng Lập trình viên PHP',
    'tuyen-dung-lap-trinh-vien-php',
    '<p>Mô tả công việc chi tiết...</p>',
    '<ul><li>Kinh nghiệm PHP 2+ năm</li><li>Biết Laravel framework</li></ul>',
    '<ul><li>Lương cạnh tranh</li><li>Bảo hiểm đầy đủ</li></ul>',
    'recruitment-image.jpg',
    '15-25 triệu',
    'Hà Nội',
    '2024-02-28',
    'Lập trình viên',
    'open'
);

-- Thêm dữ liệu mẫu cho permissions
INSERT INTO permissions (name, description) VALUES
('news_create', 'Tạo tin tức mới'),
('news_edit', 'Chỉnh sửa tin tức'),
('news_delete', 'Xóa tin tức'),
('news_publish', 'Xuất bản tin tức'),
('recruitment_create', 'Tạo tin tuyển dụng'),
('recruitment_edit', 'Chỉnh sửa tin tuyển dụng'),
('recruitment_delete', 'Xóa tin tuyển dụng'),
('user_manage', 'Quản lý người dùng'),
('category_manage', 'Quản lý danh mục');

-- Gán quyền cho các vai trò
INSERT INTO role_permissions (role, permission_id) VALUES
('admin', 1), ('admin', 2), ('admin', 3), ('admin', 4), ('admin', 5),
('admin', 6), ('admin', 7), ('admin', 8), ('admin', 9),
('editor', 1), ('editor', 2), ('editor', 4),
('author', 1), ('author', 2),
('recruiter', 5), ('recruiter', 6);

-- Thêm dữ liệu mẫu cho contact_categories
INSERT INTO contact_categories (name, slug, description) VALUES
('Hỗ trợ kỹ thuật', 'ho-tro-ky-thuat', 'Các vấn đề về kỹ thuật website'),
('Thắc mắc sản phẩm', 'thac-mac-san-pham', 'Câu hỏi về sản phẩm/dịch vụ'),
('Tuyển dụng', 'tuyen-dung', 'Liên hệ về tuyển dụng'),
('Hợp tác', 'hop-tac', 'Đề xuất hợp tác kinh doanh'),
('Phản hồi', 'phan-hoi', 'Ý kiến phản hồi từ khách hàng'),
('Khiếu nại', 'khieu-nai', 'Khiếu nại về dịch vụ'),
('Khác', 'khac', 'Các liên hệ khác');

-- Thêm dữ liệu mẫu cho response_templates
INSERT INTO response_templates (title, slug, subject, content, category_id) VALUES
('Xác nhận tiếp nhận', 'xac-nhan-tiep-nhan', 'Đã nhận được liên hệ của bạn', 'Kính gửi {customer_name},\n\nChúng tôi đã nhận được thông tin liên hệ của bạn và sẽ phản hồi trong thời gian sớm nhất.\n\nTrân trọng,\nĐội ngũ hỗ trợ', 1),
('Phản hồi tuyển dụng', 'phan-hoi-tuyen-dung', 'Thông tin tuyển dụng', 'Kính gửi {customer_name},\n\nCảm ơn bạn đã quan tâm đến vị trí tuyển dụng. Chúng tôi sẽ xem xét hồ sơ của bạn và liên hệ trong thời gian tới.\n\nTrân trọng,\nPhòng Nhân sự', 3);

-- Thêm dữ liệu mẫu cho contacts
INSERT INTO contacts (
    customer_name, phone, email, content, contact_type, category_id, source, status, priority
) VALUES 
('Nguyễn Văn A', '0909123456', 'nguyena@gmail.com', 'Tôi cần hỗ trợ về sản phẩm X', 'support', 2, 'website', 'new', 'medium'),
('Trần Thị B', '0918234567', 'tranthib@yahoo.com', 'Ứng tuyển vị trí lập trình viên', 'recruitment', 3, 'website', 'new', 'high'),
('Lê Văn C', '0987654321', 'levanc@gmail.com', 'Website bị lỗi không đăng nhập được', 'support', 1, 'mobile', 'processing', 'urgent'),
('Phạm Thị D', '0978123456', NULL, 'Đề xuất hợp tác kinh doanh', 'partnership', 4, 'email', 'read', 'low'),
('Hoàng Văn E', '0967890123', 'hoange@gmail.com', 'Khiếu nại về chất lượng dịch vụ', 'complaint', 6, 'website', 'replied', 'high');

-- Thêm dữ liệu mẫu cho contact_histories
INSERT INTO contact_histories (contact_id, action, note) VALUES
(1, 'note_added', 'Đã liên hệ khách hàng qua điện thoại, chờ phản hồi'),
(3, 'assigned', 'Phân công cho đội kỹ thuật xử lý'),
(5, 'replied', 'Đã gửi email phản hồi và xin lỗi khách hàng');

-- Tạo procedure CheckLogin
DELIMITER $$

CREATE PROCEDURE CheckLogin(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255)
)
BEGIN
    DECLARE v_user_id INT;
    DECLARE v_password_hash VARCHAR(255);
    DECLARE v_status VARCHAR(20);
    DECLARE v_login_attempts INT;
    
    -- Lấy thông tin user
    SELECT id, password_hash, status, login_attempts 
    INTO v_user_id, v_password_hash, v_status, v_login_attempts
    FROM authors 
    WHERE username = p_username OR email = p_username;
    
    -- Kiểm tra user có tồn tại
    IF v_user_id IS NULL THEN
        SELECT FALSE AS success, 'Tên đăng nhập không tồn tại' AS message, NULL AS user_id;
    ELSE
        -- Kiểm tra tài khoản có bị khóa không
        IF v_status != 'active' THEN
            SELECT FALSE AS success, 'Tài khoản đã bị khóa' AS message, NULL AS user_id;
        ELSE
            -- Kiểm tra mật khẩu (sử dụng bcrypt)
            -- Trong thực tế, bạn sẽ so sánh bằng hàm password_verify() trong PHP
            -- Ở đây chỉ là ví dụ
            IF p_password = '123456' THEN -- Thay bằng password_verify() trong PHP
                -- Reset login attempts
                UPDATE authors 
                SET login_attempts = 0, 
                    last_login = CURRENT_TIMESTAMP 
                WHERE id = v_user_id;
                
                -- Log đăng nhập thành công
                INSERT INTO login_logs (author_id, username, success) 
                VALUES (v_user_id, p_username, TRUE);
                
                SELECT TRUE AS success, 'Đăng nhập thành công' AS message, v_user_id AS user_id;
            ELSE
                -- Tăng số lần thất bại
                UPDATE authors 
                SET login_attempts = v_login_attempts + 1
                WHERE id = v_user_id;
                
                -- Log đăng nhập thất bại
                INSERT INTO login_logs (author_id, username, success, failure_reason) 
                VALUES (v_user_id, p_username, FALSE, 'Sai mật khẩu');
                
                SELECT FALSE AS success, 'Sai mật khẩu' AS message, NULL AS user_id;
            END IF;
        END IF;
    END IF;
END$$

DELIMITER ;

-- Tạo trigger before_login_log_insert
DELIMITER $$

CREATE TRIGGER before_login_log_insert
BEFORE INSERT ON login_logs
FOR EACH ROW
BEGIN
    DECLARE v_attempts INT;
    
    IF NEW.success = FALSE THEN
        -- Đếm số lần thất bại trong 30 phút
        SELECT COUNT(*) INTO v_attempts
        FROM login_logs
        WHERE author_id = NEW.author_id
        AND success = FALSE
        AND login_time > NOW() - INTERVAL 30 MINUTE;
        
        -- Nếu thất bại 5 lần trong 30 phút, khóa tài khoản
        IF v_attempts >= 5 THEN
            UPDATE authors 
            SET status = 'suspended' 
            WHERE id = NEW.author_id;
        END IF;
    END IF;
END$$

DELIMITER ;

-- Tạo trigger after_contact_insert
DELIMITER $$

CREATE TRIGGER after_contact_insert
AFTER INSERT ON contacts
FOR EACH ROW
BEGIN
    INSERT INTO contact_histories (contact_id, action, new_value, created_at)
    VALUES (NEW.id, 'created', CONCAT('Liên hệ mới từ ', NEW.customer_name), NOW());
END$$

DELIMITER ;

-- Tạo trigger after_contact_update
DELIMITER $$

CREATE TRIGGER after_contact_update
AFTER UPDATE ON contacts
FOR EACH ROW
BEGIN
    -- Ghi log khi status thay đổi
    IF OLD.status != NEW.status THEN
        INSERT INTO contact_histories (contact_id, action, old_value, new_value, created_at)
        VALUES (NEW.id, 'status_changed', OLD.status, NEW.status, NOW());
    END IF;
    
    -- Ghi log khi priority thay đổi
    IF OLD.priority != NEW.priority THEN
        INSERT INTO contact_histories (contact_id, action, old_value, new_value, created_at)
        VALUES (NEW.id, 'priority_changed', OLD.priority, NEW.priority, NOW());
    END IF;
    
    -- Ghi log khi assigned_to thay đổi
    IF OLD.assigned_to != NEW.assigned_to THEN
        INSERT INTO contact_histories (contact_id, action, old_value, new_value, created_at)
        VALUES (NEW.id, 'assigned', 
                IFNULL((SELECT username FROM authors WHERE id = OLD.assigned_to), 'Chưa phân công'),
                IFNULL((SELECT username FROM authors WHERE id = NEW.assigned_to), 'Chưa phân công'),
                NOW());
    END IF;
END$$

DELIMITER ;

-- Tạo procedure AddNewContact
DELIMITER $$

CREATE PROCEDURE AddNewContact(
    IN p_name VARCHAR(100),
    IN p_phone VARCHAR(20),
    IN p_email VARCHAR(100),
    IN p_content TEXT,
    IN p_type ENUM('general', 'support', 'feedback', 'complaint', 'recruitment', 'partnership'),
    IN p_category_id INT,
    IN p_source ENUM('website', 'mobile', 'email', 'phone', 'social'),
    IN p_ip VARCHAR(45),
    IN p_user_agent TEXT,
    IN p_page_url VARCHAR(500),
    IN p_referrer VARCHAR(500)
)
BEGIN
    DECLARE v_default_assignee INT;
    
    -- Lấy người được phân công mặc định từ category
    IF p_category_id IS NOT NULL THEN
        SELECT default_assignee INTO v_default_assignee
        FROM contact_categories
        WHERE id = p_category_id;
    END IF;
    
    -- Chèn dữ liệu vào bảng contacts
    INSERT INTO contacts (
        customer_name, phone, email, content, contact_type, 
        category_id, source, ip_address, user_agent, page_url, 
        referrer_url, assigned_to
    ) VALUES (
        p_name, p_phone, p_email, p_content, p_type,
        p_category_id, p_source, p_ip, p_user_agent, p_page_url,
        p_referrer, v_default_assignee
    );
    
    SELECT LAST_INSERT_ID() as contact_id;
END$$

DELIMITER ;

-- Tạo view vw_contact_summary
CREATE VIEW vw_contact_summary AS
SELECT 
    c.id,
    c.customer_name,
    c.phone,
    c.email,
    SUBSTRING(c.content, 1, 100) as content_preview,
    c.contact_type,
    cat.name as category_name,
    c.status,
    c.priority,
    c.created_at,
    c.response_at,
    a.username as assigned_to,
    DATEDIFF(NOW(), c.created_at) as days_old,
    COUNT(DISTINCT att.id) as attachment_count,
    COUNT(DISTINCT h.id) as history_count
FROM contacts c
LEFT JOIN contact_categories cat ON c.category_id = cat.id
LEFT JOIN authors a ON c.assigned_to = a.id
LEFT JOIN contact_attachments att ON c.id = att.contact_id
LEFT JOIN contact_histories h ON c.id = h.contact_id
GROUP BY c.id
ORDER BY c.created_at DESC;

-- Các truy vấn mẫu

-- Lấy tất cả tin tức đã xuất bản
SELECT n.*, c.name as category_name 
FROM news n
LEFT JOIN categories c ON n.category_id = c.id
WHERE n.status = 'published'
ORDER BY n.publish_date DESC;

-- Lấy tin tuyển dụng đang mở
SELECT * FROM recruitments 
WHERE status = 'open' 
AND (deadline IS NULL OR deadline >= CURDATE())
ORDER BY created_at DESC;

-- Tìm kiếm tin tức
SELECT * FROM news 
WHERE (title LIKE '%công nghệ%' OR content LIKE '%công nghệ%')
AND status = 'published';

-- Thống kê số lượng tin theo thể loại
SELECT c.name, COUNT(n.id) as news_count
FROM categories c
LEFT JOIN news n ON c.id = n.category_id
GROUP BY c.id;

-- Xem lịch sử đăng nhập
SELECT 
    ll.login_time,
    a.username,
    a.full_name,
    ll.ip_address,
    CASE WHEN ll.success THEN 'Thành công' ELSE 'Thất bại' END as status,
    ll.failure_reason
FROM login_logs ll
LEFT JOIN authors a ON ll.author_id = a.id
ORDER BY ll.login_time DESC
LIMIT 50;

-- Đếm số lần đăng nhập thất bại gần đây
SELECT 
    a.username,
    COUNT(*) as failed_attempts,
    MAX(ll.login_time) as last_attempt
FROM login_logs ll
JOIN authors a ON ll.author_id = a.id
WHERE ll.success = FALSE
AND ll.login_time > NOW() - INTERVAL 24 HOUR
GROUP BY a.id
HAVING failed_attempts >= 3;

-- Reset mật khẩu
UPDATE authors 
SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Mật khẩu mới
    reset_token = NULL,
    reset_token_expiry = NULL
WHERE id = 1;

-- Khóa tài khoản
UPDATE authors SET status = 'suspended' WHERE id = 1;

-- Mở khóa tài khoản
UPDATE authors SET status = 'active', login_attempts = 0 WHERE id = 1;

-- Đếm số liên hệ theo trạng thái
SELECT 
    status,
    COUNT(*) as count,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM contacts), 2) as percentage
FROM contacts
GROUP BY status
ORDER BY FIELD(status, 'new', 'processing', 'read', 'replied', 'resolved');

-- Thống kê liên hệ theo ngày
SELECT 
    DATE(created_at) as date,
    COUNT(*) as total_contacts,
    SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_contacts,
    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_contacts
FROM contacts
WHERE created_at >= CURDATE() - INTERVAL 30 DAY
GROUP BY DATE(created_at)
ORDER BY date DESC;

-- Liên hệ cần xử lý (new, read, processing)
SELECT 
    c.*,
    cat.name as category_name,
    a.username as assigned_to_name
FROM contacts c
LEFT JOIN contact_categories cat ON c.category_id = cat.id
LEFT JOIN authors a ON c.assigned_to = a.id
WHERE c.status IN ('new', 'read', 'processing')
ORDER BY 
    FIELD(c.priority, 'urgent', 'high', 'medium', 'low'),
    c.created_at;

-- Tìm kiếm liên hệ
SELECT * FROM contacts 
WHERE 
    (customer_name LIKE '%{keyword}%' OR 
     email LIKE '%{keyword}%' OR 
     phone LIKE '%{keyword}%' OR 
     content LIKE '%{keyword}%')
    AND (status = '{status}' OR '{status}' = '')
    AND (contact_type = '{type}' OR '{type}' = '')
ORDER BY created_at DESC;

-- Thống kê hiệu suất xử lý
SELECT 
    a.username,
    a.full_name,
    COUNT(DISTINCT c.id) as total_assigned,
    SUM(CASE WHEN c.status = 'resolved' THEN 1 ELSE 0 END) as resolved,
    ROUND(AVG(TIMESTAMPDIFF(HOUR, c.created_at, c.response_at)), 1) as avg_response_hours
FROM contacts c
JOIN authors a ON c.assigned_to = a.id
WHERE c.created_at >= CURDATE() - INTERVAL 30 DAY
GROUP BY a.id
ORDER BY resolved DESC;
/////////////
-- Tạo bảng lưu trữ liên kết
CREATE TABLE links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(500) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    link_type ENUM('internal', 'external', 'anchor', 'file') DEFAULT 'internal',
    target ENUM('_self', '_blank', '_parent', '_top') DEFAULT '_blank',
    rel VARCHAR(100) DEFAULT 'noopener noreferrer',
    icon_class VARCHAR(100),
    image_url VARCHAR(500),
    sort_order INT DEFAULT 0,
    parent_id INT DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    click_count INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES links(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES authors(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo index
CREATE INDEX idx_links_slug ON links(slug);
CREATE INDEX idx_links_type ON links(link_type);
CREATE INDEX idx_links_parent ON links(parent_id);
CREATE INDEX idx_links_active ON links(is_active);
CREATE INDEX idx_links_sort ON links(sort_order);

CREATE TABLE news_related_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    news_id INT NOT NULL,
    related_type ENUM('news', 'recruitment', 'link', 'category') NOT NULL,
    related_id INT NOT NULL,
    title VARCHAR(255),
    url VARCHAR(500),
    link_order INT DEFAULT 0,
    relationship_type ENUM('related', 'recommended', 'similar', 'series') DEFAULT 'related',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (news_id) REFERENCES news(id) ON DELETE CASCADE,
    UNIQUE KEY unique_news_related (news_id, related_type, related_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_news_related ON news_related_links(news_id);
CREATE INDEX idx_related_type ON news_related_links(related_type, related_id);

CREATE TABLE recruitment_related_links (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recruitment_id INT NOT NULL,
    related_type ENUM('news', 'recruitment', 'link', 'category') NOT NULL,
    related_id INT NOT NULL,
    title VARCHAR(255),
    url VARCHAR(500),
    link_order INT DEFAULT 0,
    relationship_type ENUM('related', 'similar_job', 'same_company', 'same_location') DEFAULT 'related',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (recruitment_id) REFERENCES recruitments(id) ON DELETE CASCADE,
    UNIQUE KEY unique_recruitment_related (recruitment_id, related_type, related_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_recruitment_related ON recruitment_related_links(recruitment_id);
CREATE INDEX idx_rec_related_type ON recruitment_related_links(related_type, related_id);

CREATE TABLE menus (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    location ENUM('header', 'footer', 'sidebar', 'mobile', 'quick_links') NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_id INT NOT NULL,
    parent_id INT DEFAULT NULL,
    link_id INT,
    custom_title VARCHAR(255),
    custom_url VARCHAR(500),
    icon_class VARCHAR(100),
    css_class VARCHAR(100),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (menu_id) REFERENCES menus(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    FOREIGN KEY (link_id) REFERENCES links(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_menu_items_menu ON menu_items(menu_id);
CREATE INDEX idx_menu_items_parent ON menu_items(parent_id);
CREATE INDEX idx_menu_items_order ON menu_items(sort_order);

CREATE TABLE breadcrumb_paths (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entity_type ENUM('news', 'recruitment', 'category', 'page') NOT NULL,
    entity_id INT NOT NULL,
    path_json JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_breadcrumb (entity_type, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_breadcrumb_entity ON breadcrumb_paths(entity_type, entity_id);

-- Thêm các menu chính
INSERT INTO menus (name, slug, location, description) VALUES
('Menu chính', 'main-menu', 'header', 'Menu điều hướng chính'),
('Menu footer', 'footer-menu', 'footer', 'Menu chân trang'),
('Liên kết nhanh', 'quick-links', 'sidebar', 'Liên kết nhanh trong sidebar');

-- Thêm các link thông dụng
INSERT INTO links (title, url, slug, link_type, target) VALUES
('Trang chủ', '/', 'home', 'internal', '_self'),
('Tin tức', '/tin-tuc', 'news', 'internal', '_self'),
('Tuyển dụng', '/tuyen-dung', 'recruitment', 'internal', '_self'),
('Liên hệ', '/lien-he', 'contact', 'internal', '_self'),
('Giới thiệu', '/gioi-thieu', 'about', 'internal', '_self'),
('Facebook', 'https://facebook.com/yourpage', 'facebook', 'external', '_blank'),
('YouTube', 'https://youtube.com/yourchannel', 'youtube', 'external', '_blank');

-- Thêm menu items
INSERT INTO menu_items (menu_id, link_id, sort_order) VALUES
(1, 1, 1), -- Trang chủ
(1, 2, 2), -- Tin tức
(1, 3, 3), -- Tuyển dụng
(1, 5, 4), -- Giới thiệu
(1, 4, 5), -- Liên hệ
(2, 6, 1), -- Facebook footer
(2, 7, 2); -- YouTube footer

DELIMITER $$

-- Procedure tạo liên kết cho tin tức
CREATE PROCEDURE GenerateNewsLinks(IN p_news_id INT)
BEGIN
    DECLARE v_title VARCHAR(255);
    DECLARE v_category_id INT;
    DECLARE v_slug VARCHAR(255);
    
    -- Lấy thông tin tin tức
    SELECT title, category_id, slug INTO v_title, v_category_id, v_slug
    FROM news WHERE id = p_news_id;
    
    -- Tạo link cho tin tức này
    INSERT INTO links (title, url, slug, link_type, created_by)
    VALUES (
        v_title,
        CONCAT('/tin-tuc/', v_slug),
        CONCAT('news-', p_news_id),
        'internal',
        (SELECT author_id FROM news WHERE id = p_news_id)
    )
    ON DUPLICATE KEY UPDATE
        title = v_title,
        url = CONCAT('/tin-tuc/', v_slug),
        updated_at = CURRENT_TIMESTAMP;
    
    -- Tìm và thêm các tin tức liên quan cùng category
    INSERT INTO news_related_links (news_id, related_type, related_id, title, url, relationship_type)
    SELECT 
        p_news_id,
        'news',
        n.id,
        n.title,
        CONCAT('/tin-tuc/', n.slug),
        'related'
    FROM news n
    WHERE n.id != p_news_id
    AND n.category_id = v_category_id
    AND n.status = 'published'
    ORDER BY n.publish_date DESC
    LIMIT 5
    ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;
END$$

-- Procedure tạo liên kết cho tin tuyển dụng
CREATE PROCEDURE GenerateRecruitmentLinks(IN p_recruitment_id INT)
BEGIN
    DECLARE v_title VARCHAR(255);
    DECLARE v_slug VARCHAR(255);
    
    -- Lấy thông tin tin tuyển dụng
    SELECT recruitment_title, slug INTO v_title, v_slug
    FROM recruitments WHERE id = p_recruitment_id;
    
    -- Tạo link cho tin tuyển dụng
    INSERT INTO links (title, url, slug, link_type, created_by)
    VALUES (
        v_title,
        CONCAT('/tuyen-dung/', v_slug),
        CONCAT('recruitment-', p_recruitment_id),
        'internal',
        (SELECT author_id FROM recruitments WHERE id = p_recruitment_id)
    )
    ON DUPLICATE KEY UPDATE
        title = v_title,
        url = CONCAT('/tuyen-dung/', v_slug),
        updated_at = CURRENT_TIMESTAMP;
    
    -- Thêm liên kết đến trang tin tức tuyển dụng
    INSERT INTO recruitment_related_links (recruitment_id, related_type, related_id, title, url, relationship_type)
    SELECT 
        p_recruitment_id,
        'news',
        n.id,
        n.title,
        CONCAT('/tin-tuc/', n.slug),
        'related'
    FROM news n
    WHERE n.title LIKE '%tuyển dụng%'
    OR n.content LIKE '%tuyển dụng%'
    AND n.status = 'published'
    ORDER BY n.publish_date DESC
    LIMIT 3
    ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;
END$$

DELIMITER ;

DELIMITER $$

-- Trigger khi tin tức được publish
CREATE TRIGGER after_news_publish
AFTER UPDATE ON news
FOR EACH ROW
BEGIN
    IF NEW.status = 'published' AND OLD.status != 'published' THEN
        CALL GenerateNewsLinks(NEW.id);
    END IF;
END$$

-- Trigger khi tin tuyển dụng được mở
CREATE TRIGGER after_recruitment_open
AFTER UPDATE ON recruitments
FOR EACH ROW
BEGIN
    IF NEW.status = 'open' AND OLD.status != 'open' THEN
        CALL GenerateRecruitmentLinks(NEW.id);
    END IF;
END$$

DELIMITER ;

CREATE VIEW vw_news_links AS
SELECT 
    n.id as news_id,
    n.title as news_title,
    n.slug as news_slug,
    l.id as link_id,
    l.title as link_title,
    l.url as link_url,
    l.link_type,
    l.target,
    nrl.relationship_type,
    nrl.link_order
FROM news n
LEFT JOIN news_related_links nrl ON n.id = nrl.news_id
LEFT JOIN links l ON (
    (nrl.related_type = 'link' AND nrl.related_id = l.id) OR
    (nrl.related_type = 'news' AND l.slug = CONCAT('news-', nrl.related_id)) OR
    (nrl.related_type = 'recruitment' AND l.slug = CONCAT('recruitment-', nrl.related_id))
)
WHERE n.status = 'published'
AND (l.id IS NULL OR l.is_active = 1)
ORDER BY n.id, nrl.link_order;

CREATE VIEW vw_recruitment_links AS
SELECT 
    r.id as recruitment_id,
    r.recruitment_title,
    r.slug as recruitment_slug,
    l.id as link_id,
    l.title as link_title,
    l.url as link_url,
    l.link_type,
    l.target,
    rrl.relationship_type,
    rrl.link_order
FROM recruitments r
LEFT JOIN recruitment_related_links rrl ON r.id = rrl.recruitment_id
LEFT JOIN links l ON (
    (rrl.related_type = 'link' AND rrl.related_id = l.id) OR
    (rrl.related_type = 'news' AND l.slug = CONCAT('news-', rrl.related_id)) OR
    (rrl.related_type = 'recruitment' AND l.slug = CONCAT('recruitment-', rrl.related_id))
)
WHERE r.status = 'open'
AND (l.id IS NULL OR l.is_active = 1)
ORDER BY r.id, rrl.link_order;

-- Lấy đường dẫn cho breadcrumb của tin tức
SELECT 
    JSON_UNQUOTE(JSON_EXTRACT(path_json, CONCAT('$[', idx, '].title'))) as title,
    JSON_UNQUOTE(JSON_EXTRACT(path_json, CONCAT('$[', idx, '].url'))) as url
FROM breadcrumb_paths bp
CROSS JOIN (
    SELECT 0 as idx UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
) numbers
WHERE bp.entity_type = 'news'
AND bp.entity_id = {news_id}
AND idx < JSON_LENGTH(path_json)
ORDER BY idx;

-- Lấy menu theo vị trí
SELECT 
    mi.id,
    mi.custom_title,
    mi.custom_url,
    l.title as link_title,
    l.url as link_url,
    COALESCE(mi.custom_title, l.title) as display_title,
    COALESCE(mi.custom_url, l.url) as display_url,
    mi.icon_class,
    mi.css_class,
    mi.sort_order
FROM menu_items mi
LEFT JOIN links l ON mi.link_id = l.id
JOIN menus m ON mi.menu_id = m.id
WHERE m.slug = 'main-menu'
AND mi.is_active = 1
AND m.is_active = 1
ORDER BY mi.sort_order;

-- Lấy liên kết liên quan cho tin tức
SELECT 
    CASE 
        WHEN nrl.related_type = 'news' THEN 'Tin tức liên quan'
        WHEN nrl.related_type = 'recruitment' THEN 'Tin tuyển dụng'
        WHEN nrl.related_type = 'link' THEN 'Liên kết'
        ELSE 'Danh mục'
    END as section_title,
    COALESCE(nrl.title, l.title) as title,
    COALESCE(nrl.url, l.url) as url,
    l.target,
    l.icon_class
FROM news_related_links nrl
LEFT JOIN links l ON (
    (nrl.related_type = 'link' AND nrl.related_id = l.id) OR
    (nrl.related_type = 'news' AND l.slug = CONCAT('news-', nrl.related_id)) OR
    (nrl.related_type = 'recruitment' AND l.slug = CONCAT('recruitment-', nrl.related_id))
)
WHERE nrl.news_id = {news_id}
AND (l.id IS NULL OR l.is_active = 1)
ORDER BY nrl.relationship_type, nrl.link_order;

-- Lấy sitemap cho SEO
SELECT 
    'news' as type,
    CONCAT('/tin-tuc/', slug) as url,
    updated_at as lastmod,
    'weekly' as changefreq,
    0.8 as priority
FROM news 
WHERE status = 'published'
UNION ALL
SELECT 
    'recruitment' as type,
    CONCAT('/tuyen-dung/', slug) as url,
    updated_at as lastmod,
    'weekly' as changefreq,
    0.7 as priority
FROM recruitments 
WHERE status = 'open'
UNION ALL
SELECT 
    'page' as type,
    url,
    updated_at as lastmod,
    'monthly' as changefreq,
    0.5 as priority
FROM links 
WHERE link_type = 'internal' 
AND is_active = 1
AND url NOT LIKE '%admin%'
ORDER BY priority DESC, lastmod DESC;