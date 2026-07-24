-- =====================================================
-- Bảng recruitments (Tuyển dụng)
-- =====================================================
CREATE TABLE `recruitments` (
    `recruitments_id` INT AUTO_INCREMENT PRIMARY KEY,
    `recruitments_title` VARCHAR(255) NOT NULL COMMENT 'Tiêu đề tin tuyển dụng',
    `slug` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Slug URL',
    `image` VARCHAR(255) DEFAULT 'default-job.webp' COMMENT 'Ảnh đại diện',
    `work_location` TEXT NULL COMMENT 'Địa điểm làm việc',
    `degree` VARCHAR(100) DEFAULT 'Cao Đẳng - Đại Học' COMMENT 'Trình độ yêu cầu',
    `work_type` VARCHAR(50) DEFAULT 'Toàn thời gian' COMMENT 'Hình thức làm việc',
    `quantity` INT DEFAULT 1 COMMENT 'Số lượng cần tuyển',
    `salary_range` VARCHAR(255) NULL COMMENT 'Mức lương',
    `deadline` DATE NOT NULL COMMENT 'Hạn nộp hồ sơ',
    `description` TEXT NULL COMMENT 'Mô tả công việc',
    `requirements` TEXT NULL COMMENT 'Yêu cầu ứng viên',
    `benefits` TEXT NULL COMMENT 'Quyền lợi được hưởng',
    `status` TINYINT DEFAULT 0 COMMENT '0-Draft, 1-Open, 2-Closed',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_deadline (deadline),
    INDEX idx_work_type (work_type),
    INDEX idx_slug (slug)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- =====================================================
-- Insert dữ liệu mẫu
-- =====================================================
INSERT INTO `recruitments` (
    `title`, 
    `slug`, 
    `work_location`, 
    `degree`, 
    `work_type`, 
    `quantity`,
    `salary_range`,
    `deadline`, 
    `description`, 
    `requirements`, 
    `benefits`, 
    `image`, 
    `status`
) VALUES 
(
    'Trưởng phòng nguồn vốn',
    'truong-phong-nguon-von',
    'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
    'Cao Đẳng - Đại Học',
    'Toàn thời gian',
    1,
    '30.000.000 - 50.000.000 VNĐ',
    '2025-11-22',
    '<p>Quản lý và điều hành các hoạt động liên quan đến nguồn vốn của công ty. Xây dựng chiến lược huy động vốn và quản lý dòng tiền hiệu quả.</p>',
    '<ul><li>Tốt nghiệp Đại học chuyên ngành Tài chính - Ngân hàng</li><li>Có ít nhất 5 năm kinh nghiệm trong lĩnh vực quản lý nguồn vốn</li><li>Kỹ năng lãnh đạo và quản lý đội nhóm tốt</li></ul>',
    '<ul><li>Mức lương cạnh tranh</li><li>Thưởng theo hiệu quả công việc</li><li>Bảo hiểm đầy đủ</li><li>Môi trường làm việc chuyên nghiệp</li><li>Cơ hội thăng tiến cao</li></ul>',
    'truong-phong-nguon-von.webp',
    1
),
(
    'Chuyên viên đầu tư',
    'chuyen-vien-dau-tu',
    'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
    'Cao Đẳng - Đại Học',
    'Toàn thời gian',
    2,
    '15.000.000 - 25.000.000 VNĐ',
    '2025-11-22',
    '<p>Phân tích và đánh giá các cơ hội đầu tư. Lập báo cáo phân tích tài chính và đề xuất các phương án đầu tư cho ban lãnh đạo.</p>',
    '<ul><li>Tốt nghiệp Đại học chuyên ngành Tài chính, Kinh tế hoặc Quản trị kinh doanh</li><li>Có ít nhất 2 năm kinh nghiệm trong lĩnh vực phân tích đầu tư</li></ul>',
    '<ul><li>Mức lương hấp dẫn</li><li>Thưởng theo dự án</li><li>Bảo hiểm đầy đủ</li><li>Đào tạo chuyên sâu</li></ul>',
    'default-job.webp',
    1
),
(
    'Chuyên viên hành chính',
    'chuyen-vien-hanh-chinh',
    'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
    'Cao Đẳng - Đại Học',
    'Toàn thời gian',
    1,
    '10.000.000 - 15.000.000 VNĐ',
    '2025-11-22',
    '<p>Quản lý các công việc hành chính văn phòng. Tiếp nhận và xử lý công văn, giấy tờ. Tổ chức các sự kiện và hoạt động nội bộ.</p>',
    '<ul><li>Tốt nghiệp Cao đẳng trở lên các chuyên ngành Quản trị văn phòng, Hành chính học</li><li>Kỹ năng tổ chức và quản lý thời gian tốt</li><li>Sử dụng thành thạo các phần mềm văn phòng</li></ul>',
    '<ul><li>Lương cạnh tranh</li><li>Bảo hiểm xã hội đầy đủ</li><li>Môi trường làm việc thân thiện</li><li>Các chế độ phúc lợi theo quy định</li></ul>',
    'default-job.webp',
    1
),
(
    'Chuyên viên nhân sự',
    'chuyen-vien-nhan-su',
    'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
    'Cao Đẳng - Đại Học',
    'Toàn thời gian',
    1,
    '12.000.000 - 18.000.000 VNĐ',
    '2025-11-21',
    '<p>Tuyển dụng và quản lý nhân sự. Xây dựng chính sách đãi ngộ và đào tạo nhân viên. Giải quyết các vấn đề về nhân sự trong công ty.</p>',
    '<ul><li>Tốt nghiệp Đại học chuyên ngành Quản trị nhân lực, Tâm lý học hoặc các ngành liên quan</li><li>Có ít nhất 2 năm kinh nghiệm làm nhân sự</li><li>Kỹ năng giao tiếp và đàm phán tốt</li></ul>',
    '<ul><li>Mức lương hấp dẫn</li><li>Thưởng hiệu suất</li><li>Bảo hiểm đầy đủ</li><li>Cơ hội phát triển nghề nghiệp</li></ul>',
    'default-job.webp',
    1
);