-- =====================================================
-- 4. Bảng recruitments
-- =====================================================
-- Tạo bảng recruitments
CREATE TABLE `recruitments`
(
    `id`            INT AUTO_INCREMENT PRIMARY KEY,
    `title`         VARCHAR(255) NOT NULL,
    `slug`          VARCHAR(255) NOT NULL UNIQUE,
    `image`         VARCHAR(255) DEFAULT 'default-job.webp',
    `work_location` TEXT         NULL,
    `degree`        VARCHAR(100) DEFAULT 'Cao Đẳng - Đại Học',
    `quantity`      INT          DEFAULT 1,
    `salary_range`  VARCHAR(100) NULL,
    `deadline`      DATE         NOT NULL,
    `description`   TEXT         NULL,
    `requirements`  TEXT         NULL,
    `benefits`      TEXT         NULL,
    `status`        TINYINT      DEFAULT 1,
    `created_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_deadline (deadline)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Insert dữ liệu từ recruitment.html
INSERT INTO `recruitments` (`title`, `slug`, `work_location`, `degree`, `quantity`, `deadline`, `image`)
VALUES ('Trưởng phòng nguồn vốn', 'truong-phong-nguon-von',
        'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh', 'Cao Đẳng - Đại Học', 1, '2025-11-22',
        'truong-phong-nguon-von-1763953822-egprx.webp'),
       ('Chuyên viên đầu tư', 'chuyen-vien-dau-tu',
        'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh', 'Cao Đẳng - Đại Học', 1, '2025-11-22',
        'truong-phong-nguon-von-1763953822-egprx.webp'),
       ('Chuyên viên hành chính', 'chuyen-vien-hanh-chinh',
        'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh', 'Cao Đẳng - Đại Học', 1, '2025-11-22',
        'truong-phong-nguon-von-1763953822-egprx.webp'),
       ('Chuyên viên nhân sự', 'chuyen-vien-nhan-su',
        'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh', 'Cao Đẳng - Đại Học', 1, '2025-11-21',
        'truong-phong-nguon-von-1763953822-egprx.webp');

-- =====================================================
-- Cập nhật cấu trúc bảng recruitments (nếu cần thay đổi)
-- =====================================================

-- Thêm cột nếu chưa có (ví dụ: benefits, requirements, salary_range, v.v.)
-- Lưu ý: Nếu cột đã tồn tại, sẽ báo lỗi, có thể kiểm tra trước hoặc bỏ qua
ALTER TABLE `recruitments`
    ADD COLUMN IF NOT EXISTS `salary_range` VARCHAR(100) NULL AFTER `quantity`,
    ADD COLUMN IF NOT EXISTS `description` TEXT NULL AFTER `deadline`,
    ADD COLUMN IF NOT EXISTS `requirements` TEXT NULL AFTER `description`,
    ADD COLUMN IF NOT EXISTS `benefits` TEXT NULL AFTER `requirements`;

-- =====================================================
-- Cập nhật dữ liệu các bản ghi đã tồn tại
-- =====================================================

