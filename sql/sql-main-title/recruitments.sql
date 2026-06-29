-- =====================================================
-- Bảng recruitments (Tuyển dụng)
-- =====================================================
-- Tạo bảng recruitments
CREATE TABLE `recruitments` (
    `recruitment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `recruitment_title` VARCHAR(255) NOT NULL COMMENT 'Tiêu đề tin tuyển dụng',
    `recruitment_slug` VARCHAR(255) NOT NULL UNIQUE COMMENT 'Slug URL',
    `work_location` TEXT NULL COMMENT 'Địa điểm làm việc',
    `degree_requirement` VARCHAR(100) DEFAULT 'Cao Đẳng - Đại Học' COMMENT 'Trình độ yêu cầu',
    `work_type` ENUM('full_time', 'part_time', 'contract') NOT NULL DEFAULT 'full_time' COMMENT 'Hình thức làm việc',
    `deadline` DATE NOT NULL COMMENT 'Hạn nộp hồ sơ',
    `description` TEXT NULL COMMENT 'Mô tả công việc',
    `requirements` TEXT NULL COMMENT 'Yêu cầu ứng viên',
    `benefits` TEXT NULL COMMENT 'Quyền lợi được hưởng',
    `avatar_img` VARCHAR(255) DEFAULT 'default-job.webp' COMMENT 'Ảnh đại diện',
    `status` TINYINT DEFAULT 1 COMMENT '0-Draft, 1-Open, 2-Closed',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_deadline (deadline),
    INDEX idx_work_type (work_type),
    INDEX idx_recruitment_slug (recruitment_slug)  -- Đổi tên index
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_general_ci;

-- =====================================================
-- Insert dữ liệu mẫu
-- =====================================================
INSERT INTO `recruitments` (
    `recruitment_title`, 
    `recruitment_slug`, 
    `work_location`, 
    `degree_requirement`, 
    `work_type`, 
    `deadline`, 
    `description`, 
    `requirements`, 
    `benefits`, 
    `avatar_img`, 
    `status`
) VALUES 
(
    'Trưởng phòng nguồn vốn',
    'truong-phong-nguon-von',
    'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
    'Cao Đẳng - Đại Học',
    'full_time',
    '2025-11-22',
    'Quản lý và điều hành các hoạt động liên quan đến nguồn vốn của công ty. Xây dựng chiến lược huy động vốn và quản lý dòng tiền hiệu quả.',
    'Tốt nghiệp Đại học chuyên ngành Tài chính - Ngân hàng. Có ít nhất 5 năm kinh nghiệm trong lĩnh vực quản lý nguồn vốn. Kỹ năng lãnh đạo và quản lý đội nhóm tốt.',
    'Mức lương cạnh tranh. Thưởng theo hiệu quả công việc. Bảo hiểm đầy đủ. Môi trường làm việc chuyên nghiệp. Cơ hội thăng tiến cao.',
    'truong-phong-nguon-von-1763953822-egprx.webp',
    1
),
(
    'Chuyên viên đầu tư',
    'chuyen-vien-dau-tu',
    'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
    'Cao Đẳng - Đại Học',
    'full_time',
    '2025-11-22',
    'Phân tích và đánh giá các cơ hội đầu tư. Lập báo cáo phân tích tài chính và đề xuất các phương án đầu tư cho ban lãnh đạo.',
    'Tốt nghiệp Đại học chuyên ngành Tài chính, Kinh tế hoặc Quản trị kinh doanh. Có ít nhất 2 năm kinh nghiệm trong lĩnh vực phân tích đầu tư.',
    'Mức lương hấp dẫn. Thưởng theo dự án. Bảo hiểm đầy đủ. Đào tạo chuyên sâu.',
    'truong-phong-nguon-von-1763953822-egprx.webp',
    1
),
(
    'Chuyên viên hành chính',
    'chuyen-vien-hanh-chinh',
    'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
    'Cao Đẳng - Đại Học',
    'full_time',
    '2025-11-22',
    'Quản lý các công việc hành chính văn phòng. Tiếp nhận và xử lý công văn, giấy tờ. Tổ chức các sự kiện và hoạt động nội bộ.',
    'Tốt nghiệp Cao đẳng trở lên các chuyên ngành Quản trị văn phòng, Hành chính học. Kỹ năng tổ chức và quản lý thời gian tốt. Sử dụng thành thạo các phần mềm văn phòng.',
    'Lương cạnh tranh. Bảo hiểm xã hội đầy đủ. Môi trường làm việc thân thiện. Các chế độ phúc lợi theo quy định.',
    'truong-phong-nguon-von-1763953822-egprx.webp',
    1
),
(
    'Chuyên viên nhân sự',
    'chuyen-vien-nhan-su',
    'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
    'Cao Đẳng - Đại Học',
    'full_time',
    '2025-11-21',
    'Tuyển dụng và quản lý nhân sự. Xây dựng chính sách đãi ngộ và đào tạo nhân viên. Giải quyết các vấn đề về nhân sự trong công ty.',
    'Tốt nghiệp Đại học chuyên ngành Quản trị nhân lực, Tâm lý học hoặc các ngành liên quan. Có ít nhất 2 năm kinh nghiệm làm nhân sự. Kỹ năng giao tiếp và đàm phán tốt.',
    'Mức lương hấp dẫn. Thưởng hiệu suất. Bảo hiểm đầy đủ. Cơ hội phát triển nghề nghiệp.',
    'truong-phong-nguon-von-1763953822-egprx.webp',
    1
);

-- =====================================================
-- Cập nhật trạng thái (Ví dụ minh họa)
-- =====================================================
-- Cập nhật trạng thái Draft cho một số bản ghi
UPDATE `recruitments` SET `status` = 0 WHERE `id` = 4;

-- Cập nhật trạng thái Closed cho một số bản ghi
-- UPDATE `recruitments` SET `status` = 2 WHERE `id` = 1;

-- =====================================================
-- Truy vấn mẫu
-- =====================================================

-- 1. Lấy danh sách tin tuyển dụng đang mở (Open)
SELECT * FROM `recruitments` WHERE `status` = 1 ORDER BY `created_at` DESC;

-- 2. Lấy danh sách tin tuyển dụng còn hạn nộp hồ sơ
SELECT * FROM `recruitments` WHERE `deadline` >= CURDATE() AND `status` = 1;

-- 3. Lấy danh sách tin tuyển dụng theo hình thức làm việc
SELECT * FROM `recruitments` WHERE `work_type` = 'full_time' AND `status` = 1;

-- 4. Đếm số lượng tin tuyển dụng theo trạng thái
SELECT 
    `status`,
    CASE 
        WHEN `status` = 0 THEN 'Draft'
        WHEN `status` = 1 THEN 'Open'
        WHEN `status` = 2 THEN 'Closed'
    END AS status_name,
    COUNT(*) AS total
FROM `recruitments`
GROUP BY `status`;