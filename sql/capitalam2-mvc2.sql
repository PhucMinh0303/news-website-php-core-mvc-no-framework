-- Tạo database
CREATE DATABASE IF NOT EXISTS quanly_tintuc
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

-- Tạo user (thay đổi username và password theo nhu cầu)
CREATE USER IF NOT EXISTS 'capitalam2'@'localhost' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON quanly_tintuc.* TO 'capitalam2'@'localhost';
FLUSH PRIVILEGES;

-- Sử dụng database
USE quanly_tintuc;

-- =====================================================
-- 1. Bảng categories
-- =====================================================
CREATE TABLE IF NOT EXISTS categories
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100)        NOT NULL,
    slug        VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu cho categories
INSERT INTO categories (name, slug, description)
VALUES ('Thời sự', 'thoi-su', 'Tin tức thời sự trong nước và quốc tế'),
       ('Kinh tế', 'kinh-te', 'Tin tức kinh tế, tài chính'),
       ('Công nghệ', 'cong-nghe', 'Tin công nghệ, khoa học kỹ thuật'),
       ('Bất động sản', 'bat-dong-san', 'Tin bất động sản, khoa học kỹ thuật'),
       ('Doanh nghiệp', 'doanh-nghiep', 'Tin doanh nghiệp, doanh nghiệp'),
       ('Tài chính quốc tế', 'tai-chinh-quoc-te', 'Tin tài chính quốc tế, tài chính'),
       ('Vĩ mô', 'vi-mo', 'Tin vĩ mô, vĩ mô'),
       ('Chứng khoán', 'chung-khoan', 'Tin chứng khoán, chứng khoán'),
       ('Ngân hàng', 'ngan-hang', 'Tin Ngân hàng, Ngân hàng');

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

-- =====================================================
-- 3. Bảng news
-- =====================================================
CREATE TABLE IF NOT EXISTS news
(
    id           INT PRIMARY KEY AUTO_INCREMENT,
    title        VARCHAR(255)        NOT NULL,
    slug         VARCHAR(255) UNIQUE NOT NULL,
    category_id  INT,
    author_id    INT,
    author       VARCHAR(100)        NOT NULL,
    publish_date DATE                NOT NULL,
    image        VARCHAR(255),
    content      LONGTEXT            NOT NULL,
    views        INT                                     DEFAULT 0,
    status       ENUM ('draft', 'published', 'archived') DEFAULT 'draft',
    created_at   TIMESTAMP                               DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP                               DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES authors (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO news (id, title, slug, category_id, author, publish_date, image, content, status, views)
VALUES
    (
        1,
        'NÂNG HẠNG - KHỞI ĐẦU CHO CÁC QUYẾT SÁCH, CẢI CÁCH MẠNH MẼ HƠN, CHUẨN MỰC HƠN VÀ KỶ LUẬT HƠN',
        'nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon',
        3,
        'Nguyễn Văn A',
        '2025-12-21',
        'img/section5/nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon-1763272692-veex8.webp',
        '<p>Nội dung chi tiết về nâng hạng và cải cách...</p>',
        'published',
        0
    ),
    (
        2,
        'Nâng hạng - khởi đầu cho các quyết sách, cải cách mạnh mẽ hơn, chuẩn mực hơn và kỷ luật hơn',
        'nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon-2',
        3,
        'Nguyễn Văn A',
        '2025-12-21',
        'img/section5/nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon-1763272625-jr69f.webp',
        '<p>Nội dung chi tiết về nâng hạng và cải cách (bài viết thứ 2)...</p>',
        'published',
        0
    ),
    (
        3,
        'Đầu tư tài chính với số vốn nhỏ - Nên hay không?',
        'dau-tu-tai-chinh-voi-so-von-nho-nen-hay-khong',
        3,
        'Nguyễn Văn A',
        '2025-11-17',
        'img/news/dau-tu-tai-chinh-voi-so-von-nho-nen-hay-khong-1763350202-indak.webp',
        '<p>Trên thị trường hiện có nhiều hình thức đầu tư giúp gia tăng số tiền nhanh chóng. Tuy nhiên, để có được lợi nhuận cao thường đòi hỏi việc đầu tư, kinh doanh số vốn khá lớn. Do đó, rất nhiều người đặt ra câu hỏi "Có thể đầu tư tài chính với số vốn nhỏ không?"</p>',
        'published',
        0
    ),
    (
        4,
        'Đầu tư tài chính 4.0 - Nhà đầu tư cần cẩn trọng với những chiêu trò lừa đảo!',
        'dau-tu-tai-chinh-4-0-nha-dau-tu-can-can-trong-voi-nhung-chieu-tro-lua-dao',
        3,
        'Nguyễn Văn A',
        '2025-11-17',
        'img/news/dau-tu-tai-chinh-4-0-nha-dau-tu-can-can-trong-voi-nhung-chieu-tro-lua-dao-1763350121-7qbwc.webp',
        '<p>Đầu tư tài chính là một lĩnh vực hấp dẫn và tiềm năng cho những ai muốn gia tăng thu nhập, khao khát đạt được tự do tài chính. Tuy nhiên, với sự phát triển của công nghệ và xu hướng đầu tư tài chính 4.0, cũng có nhiều trường hợp nhà đầu tư bị lừa đảo đầu tư tài chính thông qua những hình thức đầu tư online.</p>',
        'published',
        0
    ),
    (
        5,
        'Các App đầu tư chứng khoán uy tín nhất trên thị trường năm 2025',
        'cac-app-dau-tu-chung-khoan-uy-tin-nhat-tren-thi-truong-nam-2025',
        3,
        'Nguyễn Văn A',
        '2025-11-17',
        'img/news/cac-app-dau-tu-chung-khoan-uy-tin-nhat-tren-thi-truong-nam-2025-1763349359-ctajx.webp',
        '<p>Với sự phát triển của thị trường chứng khoán Việt Nam, nhiều app đầu tư chứng khoán được phát triển giúp nhà đầu tư thuận tiện hơn trong việc giao dịch cổ phiếu trên điện thoại. Cùng Anfin điểm danh 9 app đầu tư chứng khoán uy tín hàng đầu trên thị trường Việt Nam, giúp nhà đầu tư yên tâm giao dịch.</p>',
        'published',
        0
    ),
    (
        6,
        'Top 5 các diễn đàn đầu tư tài chính uy tín nhất hiện nay',
        'top-5-cac-dien-dan-dau-tu-tai-chinh-uy-tin-nhat-hien-nay',
        3,
        'Nguyễn Văn A',
        '2025-11-17',
        'img/news/top-5-cac-dien-dan-dau-tu-tai-chinh-uy-tin-nhat-hien-nay-1763349148-h1mp8.webp',
        '<p>Trên thị trường hiện nay có rất nhiều những diễn đàn đầu tư tài chính, tuy nhiên không phải nhà đầu tư nào cũng tìm được cho mình một diễn đàn đầu tư uy tín và chất lượng. Trong bài viết dưới đây, EMIR sẽ mang đến những thông tin hữu ích giúp quý nhà đầu tư có thể hiểu rõ hơn về diễn đàn đầu tư tài chính cũng như tìm được diễn đàn phù hợp nhất, hỗ trợ cho mình trong quá trình đầu tư.</p>',
        'published',
        0
    ),
    (
        7,
        'Quản lý tài sản cá nhân - Nên bắt đầu từ đâu',
        'quan-ly-tai-san-ca-nhan-nen-bat-dau-tu-dau',
        3,
        'Nguyễn Văn A',
        '2025-11-17',
        'img/news/quan-ly-tai-san-ca-nhan-nen-bat-dau-tu-dau-1763348797-0ws4a.webp',
        '<p>Quản lý tài sản giúp cá nhân và tổ chức đảm bảo rằng họ sử dụng tài sản một cách hiệu quả và đạt được mục tiêu tài chính của mình. Song, việc quản lý tài sản là công việc tốn khá nhiều thời gian và đòi hỏi người quản lý phải có kiến thức cũng như kinh nghiệm. Nếu bạn chưa biết bắt đầu quản lý tài sản cá nhân như thế nào, tham khảo ngay bài viết dưới đây nhé!</p>',
        'published',
        0
    );

-- Tạo index cho bảng news
CREATE INDEX idx_news_publish_date ON news (publish_date);
CREATE INDEX idx_news_category ON news (category_id);
CREATE INDEX idx_news_status ON news (status);

-- =====================================================
-- 4. Bảng recruitments
-- =====================================================
-- Tạo bảng recruitments
CREATE TABLE IF NOT EXISTS recruitments
(
    id                INT PRIMARY KEY AUTO_INCREMENT,
    recruitment_title VARCHAR(255)        NOT NULL,
    slug              VARCHAR(255) UNIQUE NOT NULL,
    job_description   LONGTEXT            NOT NULL,
    job_requirements  LONGTEXT            NOT NULL,
    job_benefits      LONGTEXT,
    image             VARCHAR(255),
    salary_range      VARCHAR(100),
    location          VARCHAR(200),
    deadline          DATE,
    quantity          INT                                                     DEFAULT 1,
    position          VARCHAR(100),
    experience        VARCHAR(100),
    education         VARCHAR(100),
    job_type          ENUM ('fulltime', 'parttime', 'contract', 'internship') DEFAULT 'fulltime',
    status            ENUM ('draft', 'open', 'closed', 'filled')              DEFAULT 'draft',
    views             INT                                                     DEFAULT 0,
    created_at        TIMESTAMP                                               DEFAULT CURRENT_TIMESTAMP,
    updated_at        TIMESTAMP                                               DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Thêm dữ liệu từ recruitment.php
INSERT INTO recruitments (recruitment_title, slug, job_description, job_requirements,
                          job_benefits, image, location, deadline, quantity, position, education, status, created_at)
VALUES ('Trưởng phòng nguồn vốn',
        'truong-phong-nguon-von',
        '<p><strong>Mô tả công việc:</strong></p>
         <ul>
           <li>Quản lý và phát triển nguồn vốn của công ty</li>
           <li>Xây dựng chiến lược huy động vốn hiệu quả</li>
           <li>Đàm phán với các đối tác tài chính, ngân hàng</li>
           <li>Quản lý danh mục đầu tư và dòng tiền</li>
           <li>Báo cáo trực tiếp lên Ban Giám đốc</li>
         </ul>',
        '<p><strong>Yêu cầu:</strong></p>
         <ul>
           <li>Tốt nghiệp Đại học chuyên ngành Tài chính - Ngân hàng, Kinh tế hoặc liên quan</li>
           <li>Có ít nhất 5 năm kinh nghiệm trong lĩnh vực tài chính, ngân hàng</li>
           <li>Kinh nghiệm quản lý đội nhóm từ 2 năm trở lên</li>
           <li>Kỹ năng đàm phán, thuyết trình xuất sắc</li>
           <li>Hiểu biết sâu về thị trường tài chính và các sản phẩm tài chính</li>
         </ul>',
        '<p><strong>Quyền lợi:</strong></p>
         <ul>
           <li>Lương thỏa thuận + thưởng theo hiệu quả công việc</li>
           <li>Được tham gia đầy đủ BHXH, BHYT, BHTN theo quy định</li>
           <li>Môi trường làm việc chuyên nghiệp, năng động</li>
           <li>Cơ hội thăng tiến cao</li>
           <li>Du lịch hàng năm, nghỉ mát cùng công ty</li>
         </ul>',
        'truong-phong-nguon-von.webp',
        'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
        '2025-11-22',
        1,
        'Trưởng phòng',
        'Cao Đẳng - Đại Học',
        'open',
        NOW()),

       ('Chuyên viên đầu tư',
        'chuyen-vien-dau-tu',
        '<p><strong>Mô tả công việc:</strong></p>
         <ul>
           <li>Nghiên cứu và phân tích các cơ hội đầu tư</li>
           <li>Đánh giá hiệu quả các dự án đầu tư</li>
           <li>Lập báo cáo phân tích tài chính</li>
           <li>Theo dõi và quản lý danh mục đầu tư</li>
           <li>Phối hợp với các phòng ban liên quan trong quá trình triển khai dự án</li>
         </ul>',
        '<p><strong>Yêu cầu:</strong></p>
         <ul>
           <li>Tốt nghiệp Đại học chuyên ngành Tài chính, Đầu tư, Kinh tế hoặc liên quan</li>
           <li>Có ít nhất 2 năm kinh nghiệm trong lĩnh vực đầu tư, phân tích tài chính</li>
           <li>Thành thạo Excel, các công cụ phân tích tài chính</li>
           <li>Kỹ năng phân tích, tổng hợp và báo cáo tốt</li>
           <li>Khả năng làm việc độc lập và theo nhóm</li>
         </ul>',
        '<p><strong>Quyền lợi:</strong></p>
         <ul>
           <li>Lương: 15-20 triệu đồng/tháng + thưởng</li>
           <li>Đầy đủ chế độ bảo hiểm theo quy định</li>
           <li>Môi trường làm việc trẻ trung, năng động</li>
           <li>Đào tạo chuyên sâu về nghiệp vụ</li>
         </ul>',
        'chuyen-vien-dau-tu.webp',
        'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
        '2025-11-22',
        1,
        'Chuyên viên',
        'Cao Đẳng - Đại Học',
        'open',
        NOW()),

       ('Chuyên viên hành chính',
        'chuyen-vien-hanh-chinh',
        '<p><strong>Mô tả công việc:</strong></p>
         <ul>
           <li>Quản lý công văn, giấy tờ, tài liệu hành chính</li>
           <li>Tiếp nhận và xử lý văn bản đến - đi</li>
           <li>Lên lịch công tác, sắp xếp cuộc họp</li>
           <li>Quản lý cơ sở vật chất, văn phòng phẩm</li>
           <li>Hỗ trợ công tác tổ chức sự kiện, hội nghị</li>
         </ul>',
        '<p><strong>Yêu cầu:</strong></p>
         <ul>
           <li>Tốt nghiệp Cao đẳng trở lên các chuyên ngành Quản trị văn phòng, Hành chính học</li>
           <li>Kỹ năng văn phòng thành thạo (Word, Excel, PowerPoint)</li>
           <li>Giao tiếp tốt, ngoại hình ưa nhìn</li>
           <li>Cẩn thận, tỉ mỉ và có tinh thần trách nhiệm cao</li>
         </ul>',
        '<p><strong>Quyền lợi:</strong></p>
         <ul>
           <li>Lương: 10-12 triệu đồng/tháng</li>
           <li>Đầy đủ chế độ phúc lợi theo quy định</li>
           <li>Môi trường làm việc thân thiện, hỗ trợ</li>
         </ul>',
        'chuyen-vien-hanh-chinh.webp',
        'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
        '2025-11-22',
        1,
        'Chuyên viên',
        'Cao Đẳng - Đại Học',
        'open',
        NOW()),

       ('Chuyên viên nhân sự',
        'chuyen-vien-nhan-su',
        '<p><strong>Mô tả công việc:</strong></p>
         <ul>
           <li>Thực hiện công tác tuyển dụng theo nhu cầu</li>
           <li>Quản lý hồ sơ nhân viên</li>
           <li>Theo dõi và tính toán lương, thưởng, phúc lợi</li>
           <li>Thực hiện các thủ tục bảo hiểm, hợp đồng lao động</li>
           <li>Xây dựng và triển khai các chương trình đào tạo</li>
         </ul>',
        '<p><strong>Yêu cầu:</strong></p>
         <ul>
           <li>Tốt nghiệp Đại học chuyên ngành Quản trị nhân sự, Tâm lý học hoặc liên quan</li>
           <li>Có ít nhất 1 năm kinh nghiệm trong lĩnh vực nhân sự</li>
           <li>Hiểu biết về luật lao động, bảo hiểm xã hội</li>
           <li>Kỹ năng giao tiếp, đàm phán tốt</li>
           <li>Trung thực, cẩn thận và bảo mật thông tin</li>
         </ul>',
        '<p><strong>Quyền lợi:</strong></p>
         <ul>
           <li>Lương: 12-15 triệu đồng/tháng</li>
           <li>Được đào tạo nâng cao nghiệp vụ</li>
           <li>Cơ hội thăng tiến rõ ràng</li>
           <li>Môi trường làm việc chuyên nghiệp</li>
         </ul>',
        'chuyen-vien-nhan-su.webp',
        'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
        '2025-11-21',
        1,
        'Chuyên viên',
        'Cao Đẳng - Đại Học',
        'open',
        NOW());

-- Tạo index cho bảng recruitments
CREATE INDEX idx_recruitments_status ON recruitments (status);
CREATE INDEX idx_recruitments_deadline ON recruitments (deadline);
CREATE INDEX idx_recruitments_job_type ON recruitments (job_type);
-- Cập nhật dữ liệu chi tiết cho bảng recruitments với nội dung đầy đủ
UPDATE recruitments
SET job_description  = '<p><strong>Mô tả công việc</strong></p>
    <ul>
        <li>Quản lý, theo dõi nhu cầu và cấp phát văn phòng phẩm, mực in, nước uống.</li>
        <li>Thực hiện các thủ tục thanh toán chi phí hành chính văn phòng.</li>
        <li>Soạn thảo, lưu trữ hồ sơ, giấy tờ liên quan.</li>
        <li>Tổ chức sự kiện và truyền thông nội bộ.</li>
        <li>Quản lý website, fanpage của công ty.</li>
        <li>Thực hiện các công việc khác theo chỉ đạo của cấp trên.</li>
    </ul>',
    job_requirements = '<p><strong>Yêu cầu công việc</strong></p>
    <ul>
        <li>Tốt nghiệp đại học chuyên ngành Kinh tế, Tài chính, Ngân hàng hoặc các ngành liên quan.</li>
        <li>Ưu tiên ứng viên có kinh nghiệm trong lĩnh vực tài chính, ngân hàng.</li>
        <li>Có khả năng giao tiếp và thuyết trình tốt.</li>
        <li>Tinh thần trách nhiệm cao, làm việc cẩn thận, tỉ mỉ.</li>
        <li>Có khả năng làm việc độc lập và theo nhóm.</li>
    </ul>',
    job_benefits     = '<p><strong>Quyền lợi</strong></p>
    <ul>
        <li>Lương thỏa thuận: 7 - 10 triệu đồng/tháng.</li>
        <li>Lương tháng 13 và thưởng các ngày Lễ, Tết theo quy định của công ty.</li>
        <li>Tham gia BHYT, BHXH theo quy định của Nhà nước.</li>
        <li>Du lịch, nghỉ mát hằng năm.</li>
        <li>Được đào tạo kỹ năng chuyên môn, làm việc trong môi trường thân thiện, khuyến khích sáng tạo.</li>
    </ul>
    <br />
    <div>
        Liên hệ trực tiếp với phòng HCNS:<br />
        Điện thoại/Zalo: Chị Phương – PHCNS: 0999 678 6789<br />
        Email: tuyendung@capitalam.vn<br />
        Bộ phận:&nbsp;Hành chính/Nhân sự
    </div>'
WHERE slug = 'truong-phong-nguon-von';

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

-- =====================================================
-- 10. Bảng contact_attachments
-- =====================================================
CREATE TABLE IF NOT EXISTS contact_attachments
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    contact_id  INT          NOT NULL,
    file_name   VARCHAR(255) NOT NULL,
    file_path   VARCHAR(500) NOT NULL,
    file_type   VARCHAR(100),
    file_size   INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index cho bảng contact_attachments
CREATE INDEX idx_attachments_contact ON contact_attachments (contact_id);

-- =====================================================
-- 11. Bảng contact_histories
-- =====================================================
CREATE TABLE IF NOT EXISTS contact_histories
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    contact_id INT                                                                                                 NOT NULL,
    author_id  INT,
    action     ENUM ('created', 'read', 'assigned', 'replied', 'status_changed', 'note_added', 'priority_changed') NOT NULL,
    old_value  TEXT,
    new_value  TEXT,
    note       TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES authors (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu cho contact_histories
INSERT INTO contact_histories (contact_id, action, note)
VALUES (1, 'note_added', 'Đã liên hệ khách hàng qua điện thoại, chờ phản hồi'),
       (3, 'assigned', 'Phân công cho đội kỹ thuật xử lý'),
       (5, 'replied', 'Đã gửi email phản hồi và xin lỗi khách hàng');

-- Tạo index cho bảng contact_histories
CREATE INDEX idx_histories_contact ON contact_histories (contact_id);
CREATE INDEX idx_histories_created_at ON contact_histories (created_at);

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

-- =====================================================
-- 13. Bảng links
-- =====================================================
CREATE TABLE IF NOT EXISTS links
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    title       VARCHAR(255)        NOT NULL,
    url         VARCHAR(500)        NOT NULL,
    slug        VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    link_type   ENUM ('internal', 'external', 'anchor', 'file') DEFAULT 'internal',
    target      ENUM ('_self', '_blank', '_parent', '_top')     DEFAULT '_blank',
    rel         VARCHAR(100)                                    DEFAULT 'noopener noreferrer',
    icon_class  VARCHAR(100),
    image_url   VARCHAR(500),
    sort_order  INT                                             DEFAULT 0,
    parent_id   INT                                             DEFAULT NULL,
    is_active   BOOLEAN                                         DEFAULT TRUE,
    click_count INT                                             DEFAULT 0,
    created_by  INT,
    created_at  TIMESTAMP                                       DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP                                       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES links (id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES authors (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index cho bảng links
CREATE INDEX idx_links_slug ON links (slug);
CREATE INDEX idx_links_type ON links (link_type);
CREATE INDEX idx_links_parent ON links (parent_id);
CREATE INDEX idx_links_active ON links (is_active);
CREATE INDEX idx_links_sort ON links (sort_order);

-- Thêm link mẫu
INSERT INTO links (title, url, slug, link_type, target)
VALUES ('Trang chủ', '/', 'home', 'internal', '_self'),
       ('Tin tức', '/tin-tuc', 'news', 'internal', '_self'),
       ('Tuyển dụng', '/tuyen-dung', 'recruitment', 'internal', '_self'),
       ('Liên hệ', '/lien-he', 'contact', 'internal', '_self'),
       ('Giới thiệu', '/gioi-thieu', 'about', 'internal', '_self'),
       ('Facebook', 'https://facebook.com/yourpage', 'facebook', 'external', '_blank'),
       ('YouTube', 'https://youtube.com/yourchannel', 'youtube', 'external', '_blank');

-- =====================================================
-- 14. Bảng news_related_links
-- =====================================================
CREATE TABLE IF NOT EXISTS news_related_links
(
    id                INT PRIMARY KEY AUTO_INCREMENT,
    news_id           INT                                              NOT NULL,
    related_type      ENUM ('news', 'recruitment', 'link', 'category') NOT NULL,
    related_id        INT                                              NOT NULL,
    title             VARCHAR(255),
    url               VARCHAR(500),
    link_order        INT                                                  DEFAULT 0,
    relationship_type ENUM ('related', 'recommended', 'similar', 'series') DEFAULT 'related',
    created_at        TIMESTAMP                                            DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE,
    UNIQUE KEY unique_news_related (news_id, related_type, related_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index
CREATE INDEX idx_news_related ON news_related_links (news_id);
CREATE INDEX idx_related_type ON news_related_links (related_type, related_id);

-- =====================================================
-- 15. Bảng recruitment_related_links
-- =====================================================
CREATE TABLE IF NOT EXISTS recruitment_related_links
(
    id                INT PRIMARY KEY AUTO_INCREMENT,
    recruitment_id    INT                                              NOT NULL,
    related_type      ENUM ('news', 'recruitment', 'link', 'category') NOT NULL,
    related_id        INT                                              NOT NULL,
    title             VARCHAR(255),
    url               VARCHAR(500),
    link_order        INT                                                              DEFAULT 0,
    relationship_type ENUM ('related', 'similar_job', 'same_company', 'same_location') DEFAULT 'related',
    created_at        TIMESTAMP                                                        DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (recruitment_id) REFERENCES recruitments (id) ON DELETE CASCADE,
    UNIQUE KEY unique_recruitment_related (recruitment_id, related_type, related_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index
CREATE INDEX idx_recruitment_related ON recruitment_related_links (recruitment_id);
CREATE INDEX idx_rec_related_type ON recruitment_related_links (related_type, related_id);

-- =====================================================
-- 16. Bảng menus
-- =====================================================
CREATE TABLE IF NOT EXISTS menus
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100)                                                  NOT NULL,
    slug        VARCHAR(100) UNIQUE                                           NOT NULL,
    location    ENUM ('header', 'footer', 'sidebar', 'mobile', 'quick_links') NOT NULL,
    description TEXT,
    is_active   BOOLEAN   DEFAULT TRUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Thêm menu mẫu
INSERT INTO menus (name, slug, location, description)
VALUES ('Menu chính', 'main-menu', 'header', 'Menu điều hướng chính'),
       ('Menu footer', 'footer-menu', 'footer', 'Menu chân trang'),
       ('Liên kết nhanh', 'quick-links', 'sidebar', 'Liên kết nhanh trong sidebar');

-- =====================================================
-- 17. Bảng menu_items
-- =====================================================
CREATE TABLE IF NOT EXISTS menu_items
(
    id           INT PRIMARY KEY AUTO_INCREMENT,
    menu_id      INT NOT NULL,
    parent_id    INT       DEFAULT NULL,
    link_id      INT,
    custom_title VARCHAR(255),
    custom_url   VARCHAR(500),
    icon_class   VARCHAR(100),
    css_class    VARCHAR(100),
    sort_order   INT       DEFAULT 0,
    is_active    BOOLEAN   DEFAULT TRUE,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (menu_id) REFERENCES menus (id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES menu_items (id) ON DELETE CASCADE,
    FOREIGN KEY (link_id) REFERENCES links (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Thêm menu items
INSERT INTO menu_items (menu_id, link_id, sort_order)
VALUES (1, 1, 1), -- Trang chủ
       (1, 2, 2), -- Tin tức
       (1, 3, 3), -- Tuyển dụng
       (1, 5, 4), -- Giới thiệu
       (1, 4, 5), -- Liên hệ
       (2, 6, 1), -- Facebook footer
       (2, 7, 2);
-- YouTube footer

-- Tạo index
CREATE INDEX idx_menu_items_menu ON menu_items (menu_id);
CREATE INDEX idx_menu_items_parent ON menu_items (parent_id);
CREATE INDEX idx_menu_items_order ON menu_items (sort_order);

-- =====================================================
-- 18. Bảng breadcrumb_paths
-- =====================================================
CREATE TABLE IF NOT EXISTS breadcrumb_paths
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    entity_type ENUM ('news', 'recruitment', 'category', 'page') NOT NULL,
    entity_id   INT                                              NOT NULL,
    path_json   JSON                                             NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_breadcrumb (entity_type, entity_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE INDEX idx_breadcrumb_entity ON breadcrumb_paths (entity_type, entity_id);

-- =====================================================
-- 19. Bảng news_title (chi tiết tin tức)
-- =====================================================
CREATE TABLE IF NOT EXISTS news_title
(
    id                     INT PRIMARY KEY AUTO_INCREMENT,
    news_id                INT                 NOT NULL,
    title                  VARCHAR(500)        NOT NULL,
    slug                   VARCHAR(500) UNIQUE NOT NULL,
    description            TEXT,
    content                LONGTEXT,
    meta_title             VARCHAR(255),
    meta_description       TEXT,
    meta_keywords          VARCHAR(500),
    featured_image         VARCHAR(500),
    featured_image_caption VARCHAR(255),
    video_url              VARCHAR(500),
    audio_url              VARCHAR(500),
    gallery_images         JSON,
    source                 VARCHAR(255),
    source_url             VARCHAR(500),
    author_note            TEXT,
    reading_time           INT                                                  DEFAULT 0,
    is_featured            BOOLEAN                                              DEFAULT FALSE,
    is_breaking            BOOLEAN                                              DEFAULT FALSE,
    is_hot                 BOOLEAN                                              DEFAULT FALSE,
    views                  INT                                                  DEFAULT 0,
    share_count            INT                                                  DEFAULT 0,
    like_count             INT                                                  DEFAULT 0,
    comment_count          INT                                                  DEFAULT 0,
    status                 ENUM ('draft', 'published', 'scheduled', 'archived') DEFAULT 'draft',
    published_at           TIMESTAMP           NULL,
    scheduled_at           TIMESTAMP           NULL,
    created_at             TIMESTAMP                                            DEFAULT CURRENT_TIMESTAMP,
    updated_at             TIMESTAMP                                            DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;




-- Tạo index cho news_title
CREATE INDEX idx_news_title_slug ON news_title (slug);
CREATE INDEX idx_news_title_status ON news_title (status);
CREATE INDEX idx_news_title_published ON news_title (published_at);
CREATE INDEX idx_news_title_featured ON news_title (is_featured);
CREATE INDEX idx_news_title_breaking ON news_title (is_breaking);
CREATE INDEX idx_news_title_hot ON news_title (is_hot);
CREATE FULLTEXT INDEX idx_news_title_search ON news_title (title, description, content);

-- =====================================================
-- 20. Bảng news_tags
-- =====================================================
CREATE TABLE IF NOT EXISTS news_tags
(
    id          INT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL UNIQUE,
    slug        VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- =====================================================
-- 21. Bảng news_tag_relations
-- =====================================================
CREATE TABLE IF NOT EXISTS news_tag_relations
(
    news_id    INT NOT NULL,
    tag_id     INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (news_id, tag_id),
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES news_tags (id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- =====================================================
-- 22. Bảng news_comments
-- =====================================================
CREATE TABLE IF NOT EXISTS news_comments
(
    id             INT PRIMARY KEY AUTO_INCREMENT,
    news_id        INT          NOT NULL,
    parent_id      INT       DEFAULT NULL,
    author_name    VARCHAR(100) NOT NULL,
    author_email   VARCHAR(100),
    author_website VARCHAR(255),
    author_ip      VARCHAR(45),
    content        TEXT         NOT NULL,
    is_approved    BOOLEAN   DEFAULT FALSE,
    like_count     INT       DEFAULT 0,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES news_comments (id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index
CREATE INDEX idx_comments_news ON news_comments (news_id);
CREATE INDEX idx_comments_approved ON news_comments (is_approved);
CREATE INDEX idx_comments_created ON news_comments (created_at);

-- =====================================================
-- 23. Bảng news_views_stats
-- =====================================================
CREATE TABLE IF NOT EXISTS news_views_stats
(
    id         INT PRIMARY KEY AUTO_INCREMENT,
    news_id    INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer   VARCHAR(500),
    viewed_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tạo index
CREATE INDEX idx_views_news ON news_views_stats (news_id);
CREATE INDEX idx_views_ip ON news_views_stats (ip_address);
CREATE INDEX idx_views_date ON news_views_stats (viewed_at);

-- =====================================================
-- 24. Bảng news_related
-- =====================================================
CREATE TABLE IF NOT EXISTS news_related
(
    id                INT PRIMARY KEY AUTO_INCREMENT,
    news_id           INT NOT NULL,
    related_news_id   INT NOT NULL,
    relationship_type ENUM ('related', 'similar', 'series', 'recommended') DEFAULT 'related',
    sort_order        INT                                                  DEFAULT 0,
    created_at        TIMESTAMP                                            DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE,
    FOREIGN KEY (related_news_id) REFERENCES news (id) ON DELETE CASCADE,
    UNIQUE KEY unique_related (news_id, related_news_id)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE INDEX idx_related_news ON news_related (news_id);

-- =====================================================
-- 25. Procedures và Triggers
-- =====================================================

DELIMITER $$

-- Procedure CheckLogin
CREATE PROCEDURE CheckLogin(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255)
)
BEGIN
    DECLARE v_user_id INT;
    DECLARE v_password_hash VARCHAR(255);
    DECLARE v_status VARCHAR(20);
    DECLARE v_login_attempts INT;

    SELECT id, password_hash, status, login_attempts
    INTO v_user_id, v_password_hash, v_status, v_login_attempts
    FROM authors
    WHERE username = p_username
       OR email = p_username;

    IF v_user_id IS NULL THEN
        SELECT FALSE AS success, 'Tên đăng nhập không tồn tại' AS message, NULL AS user_id;
    ELSE
        IF v_status != 'active' THEN
            SELECT FALSE AS success, 'Tài khoản đã bị khóa' AS message, NULL AS user_id;
        ELSE
            IF p_password = '123456' THEN
                UPDATE authors
                SET login_attempts = 0,
                    last_login     = CURRENT_TIMESTAMP
                WHERE id = v_user_id;

                INSERT INTO login_logs (author_id, username, success)
                VALUES (v_user_id, p_username, TRUE);

                SELECT TRUE AS success, 'Đăng nhập thành công' AS message, v_user_id AS user_id;
            ELSE
                UPDATE authors
                SET login_attempts = v_login_attempts + 1
                WHERE id = v_user_id;

                INSERT INTO login_logs (author_id, username, success, failure_reason)
                VALUES (v_user_id, p_username, FALSE, 'Sai mật khẩu');

                SELECT FALSE AS success, 'Sai mật khẩu' AS message, NULL AS user_id;
            END IF;
        END IF;
    END IF;
END$$


-- Procedure GenerateNewsLinks
CREATE PROCEDURE GenerateNewsLinks(IN p_news_id INT)
BEGIN
    DECLARE v_title VARCHAR(255);
    DECLARE v_category_id INT;
    DECLARE v_slug VARCHAR(255);
    DECLARE v_author_id INT;

    SELECT title, category_id, slug, author_id
    INTO v_title, v_category_id, v_slug, v_author_id
    FROM news
    WHERE id = p_news_id;
    -- Tạo link cho tin tức này
    INSERT INTO links (title, url, slug, link_type, created_by)
    VALUES (v_title,
            CONCAT('/tin-tuc/', v_slug),
            CONCAT('news-', p_news_id),
            'internal', v_author_id)

    ON DUPLICATE KEY UPDATE title      = v_title,
                            url        = CONCAT('/tin-tuc/', v_slug),
                            updated_at = CURRENT_TIMESTAMP;



    INSERT INTO news_related_links (news_id, related_type, related_id, title, url, relationship_type)
    SELECT p_news_id,
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
    LIMIT 5;


END$$

-- Procedure GenerateRecruitmentLinks
CREATE PROCEDURE GenerateRecruitmentLinks(IN p_recruitment_id INT)
BEGIN
    DECLARE v_title VARCHAR(255);
    DECLARE v_slug VARCHAR(255);

    SELECT recruitment_title, slug
    INTO v_title, v_slug
    FROM recruitments
    WHERE id = p_recruitment_id;

    INSERT INTO links (title, url, slug, link_type, created_by)
    VALUES (v_title,
            CONCAT('/tuyen-dung/', v_slug),
            CONCAT('recruitment-', p_recruitment_id),
            'internal',
            NULL)
    ON DUPLICATE KEY UPDATE title      = v_title,
                            url        = CONCAT('/tuyen-dung/', v_slug),
                            updated_at = CURRENT_TIMESTAMP;

    INSERT INTO recruitment_related_links (recruitment_id, related_type, related_id, title, url, relationship_type)
    SELECT p_recruitment_id,
           'news',
           n.id,
           n.title,
           CONCAT('/tin-tuc/', n.slug),
           'related'
    FROM news n
    WHERE (n.title LIKE '%tuyển dụng%' OR n.content LIKE '%tuyển dụng%')
      AND n.status = 'published'
    ORDER BY n.publish_date DESC
    LIMIT 3

END$$

-- Procedure AddNewContact
CREATE PROCEDURE AddNewContact(
    IN p_name VARCHAR(100),
    IN p_phone VARCHAR(20),
    IN p_email VARCHAR(100),
    IN p_content TEXT,
    IN p_type ENUM ('general', 'support', 'feedback', 'complaint', 'recruitment', 'partnership'),
    IN p_category_id INT,
    IN p_source ENUM ('website', 'mobile', 'email', 'phone', 'social'),
    IN p_ip VARCHAR(45),
    IN p_user_agent TEXT,
    IN p_page_url VARCHAR(500),
    IN p_referrer VARCHAR(500)
)
BEGIN
    DECLARE v_default_assignee INT;

    IF p_category_id IS NOT NULL THEN
        SELECT default_assignee
        INTO v_default_assignee
        FROM contact_categories
        WHERE id = p_category_id;
    END IF;

    INSERT INTO contacts (customer_name, phone, email, content, contact_type,
                          category_id, source, ip_address, user_agent, page_url,
                          referrer_url, assigned_to)
    VALUES (p_name, p_phone, p_email, p_content, p_type,
            p_category_id, p_source, p_ip, p_user_agent, p_page_url,
            p_referrer, v_default_assignee);

    SELECT LAST_INSERT_ID() as contact_id;
END$$

-- Function generate_unique_slug
CREATE FUNCTION generate_unique_slug(p_slug VARCHAR(500), p_table VARCHAR(100))
    RETURNS VARCHAR(500)
    DETERMINISTIC
BEGIN
    DECLARE v_new_slug VARCHAR(500);
    DECLARE v_counter INT DEFAULT 1;
    SET v_new_slug = p_slug;

    WHILE EXISTS (SELECT 1 FROM news_title WHERE slug = v_new_slug)
        DO
            SET v_counter = v_counter + 1;
            SET v_new_slug = CONCAT(p_slug, '-', v_counter);
        END WHILE;

    RETURN v_new_slug;
END$$

-- Trigger before_login_log_insert
CREATE TRIGGER before_login_log_insert
    BEFORE INSERT
    ON login_logs
    FOR EACH ROW
BEGIN
    DECLARE v_attempts INT;

    IF NEW.success = FALSE THEN
        SELECT COUNT(*)
        INTO v_attempts
        FROM login_logs
        WHERE author_id = NEW.author_id
          AND success = FALSE
          AND login_time > NOW() - INTERVAL 30 MINUTE;

        IF v_attempts >= 5 THEN
            UPDATE authors
            SET status = 'suspended'
            WHERE id = NEW.author_id;
        END IF;
    END IF;
END$$

-- Trigger after_contact_insert
CREATE TRIGGER after_contact_insert
    AFTER INSERT
    ON contacts
    FOR EACH ROW
BEGIN
    INSERT INTO contact_histories (contact_id, action, new_value, created_at)
    VALUES (NEW.id, 'created', CONCAT('Liên hệ mới từ ', NEW.customer_name), NOW());
END$$

-- Trigger after_contact_update
CREATE TRIGGER after_contact_update
    AFTER UPDATE
    ON contacts
    FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO contact_histories (contact_id, action, old_value, new_value, created_at)
        VALUES (NEW.id, 'status_changed', OLD.status, NEW.status, NOW());
    END IF;

    IF OLD.priority != NEW.priority THEN
        INSERT INTO contact_histories (contact_id, action, old_value, new_value, created_at)
        VALUES (NEW.id, 'priority_changed', OLD.priority, NEW.priority, NOW());
    END IF;

    IF OLD.assigned_to != NEW.assigned_to OR (OLD.assigned_to IS NULL AND NEW.assigned_to IS NOT NULL)
        OR (OLD.assigned_to IS NOT NULL AND NEW.assigned_to IS NULL) THEN
        INSERT INTO contact_histories (contact_id, action, old_value, new_value, created_at)
        VALUES (NEW.id, 'assigned',
                IFNULL((SELECT username FROM authors WHERE id = OLD.assigned_to), 'Chưa phân công'),
                IFNULL((SELECT username FROM authors WHERE id = NEW.assigned_to), 'Chưa phân công'),
                NOW());
    END IF;
END$$

-- Trigger after_news_publish
CREATE TRIGGER after_news_publish
    AFTER UPDATE
    ON news
    FOR EACH ROW
BEGIN
    IF NEW.status = 'published' AND OLD.status != 'published' THEN
        CALL GenerateNewsLinks(NEW.id);
    END IF;
END$$

-- Trigger after_recruitment_open
CREATE TRIGGER after_recruitment_open
    AFTER UPDATE
    ON recruitments
    FOR EACH ROW
BEGIN
    IF NEW.status = 'open' AND OLD.status != 'open' THEN
        CALL GenerateRecruitmentLinks(NEW.id);
    END IF;
END$$

DELIMITER ;

-- =====================================================
-- 26. Views
-- =====================================================

-- View vw_contact_summary
CREATE OR REPLACE VIEW vw_contact_summary AS
SELECT c.id,
       c.customer_name,
       c.phone,
       c.email,
       SUBSTRING(c.content, 1, 100)  as content_preview,
       c.contact_type,
       cat.name                      as category_name,
       c.status,
       c.priority,
       c.created_at,
       c.response_at,
       a.username                    as assigned_to,
       DATEDIFF(NOW(), c.created_at) as days_old,
       COUNT(DISTINCT att.id)        as attachment_count,
       COUNT(DISTINCT h.id)          as history_count
FROM contacts c
         LEFT JOIN contact_categories cat ON c.category_id = cat.id
         LEFT JOIN authors a ON c.assigned_to = a.id
         LEFT JOIN contact_attachments att ON c.id = att.contact_id
         LEFT JOIN contact_histories h ON c.id = h.contact_id
GROUP BY c.id
ORDER BY c.created_at DESC;

-- View vw_news_links
CREATE OR REPLACE VIEW vw_news_links AS
SELECT n.id    as news_id,
       n.title as news_title,
       n.slug  as news_slug,
       l.id    as link_id,
       l.title as link_title,
       l.url   as link_url,
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

-- View vw_recruitment_links
CREATE OR REPLACE VIEW vw_recruitment_links AS
SELECT r.id    as recruitment_id,
       r.recruitment_title,
       r.slug  as recruitment_slug,
       l.id    as link_id,
       l.title as link_title,
       l.url   as link_url,
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

-- =====================================================
-- 27. Các câu truy vấn mẫu (đã được comment)
-- =====================================================

-- Lấy tất cả tin tức đã xuất bản
-- SELECT n.*, c.name as category_name
-- FROM news n
-- LEFT JOIN categories c ON n.category_id = c.id
-- WHERE n.status = 'published'
-- ORDER BY n.publish_date DESC;

-- Lấy tin tuyển dụng đang mở
-- SELECT * FROM recruitments
-- WHERE status = 'open'
-- AND (deadline IS NULL OR deadline >= CURDATE())
-- ORDER BY created_at DESC;

-- Tìm kiếm tin tức
-- SELECT * FROM news
-- WHERE (title LIKE '%công nghệ%' OR content LIKE '%công nghệ%')
-- AND status = 'published';

-- Thống kê số lượng tin theo thể loại
-- SELECT c.name, COUNT(n.id) as news_count
-- FROM categories c
-- LEFT JOIN news n ON c.id = n.category_id
-- GROUP BY c.id;

-- Xem lịch sử đăng nhập
-- SELECT
--     ll.login_time,
--     a.username,
--     a.full_name,
--     ll.ip_address,
--     CASE WHEN ll.success THEN 'Thành công' ELSE 'Thất bại' END as status,
--     ll.failure_reason
-- FROM login_logs ll
-- LEFT JOIN authors a ON ll.author_id = a.id
-- ORDER BY ll.login_time DESC
-- LIMIT 50;