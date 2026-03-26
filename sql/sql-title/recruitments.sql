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