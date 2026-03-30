-- Bảng quản lý bài tuyển dụng
CREATE TABLE IF NOT EXISTS `recruitment_title`
(
    `id`             INT(11)      NOT NULL AUTO_INCREMENT,
    `slug`           VARCHAR(255) NOT NULL COMMENT 'Đường dẫn thân thiện',
    `title`          VARCHAR(255) NOT NULL COMMENT 'Tiêu đề công việc',
    `position`       VARCHAR(255) NOT NULL COMMENT 'Vị trí tuyển dụng',
    `description`    TEXT         NOT NULL COMMENT 'Mô tả công việc',
    `requirements`   TEXT         NOT NULL COMMENT 'Yêu cầu công việc',
    `benefits`       TEXT         NOT NULL COMMENT 'Quyền lợi',
    `work_location`  VARCHAR(500) NOT NULL COMMENT 'Nơi làm việc',
    `degree`         VARCHAR(100) NOT NULL COMMENT 'Bằng cấp yêu cầu',
    `quantity`       INT(5)       NOT NULL DEFAULT 1 COMMENT 'Số lượng tuyển',
    `deadline`       DATE         NOT NULL COMMENT 'Hạn nộp hồ sơ',
    `salary`         VARCHAR(100)          DEFAULT 'Thỏa thuận' COMMENT 'Mức lương',
    `contact_person` VARCHAR(100)          DEFAULT NULL COMMENT 'Người liên hệ',
    `contact_phone`  VARCHAR(20)           DEFAULT NULL COMMENT 'Số điện thoại liên hệ',
    `contact_email`  VARCHAR(100)          DEFAULT NULL COMMENT 'Email liên hệ',
    `image`          VARCHAR(500)          DEFAULT NULL COMMENT 'Ảnh đại diện',
    `status`         TINYINT(1)   NOT NULL DEFAULT 1 COMMENT 'Trạng thái: 1-Hiển thị, 0-Ẩn',
    `featured`       TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'Nổi bật',
    `views`          INT(11)      NOT NULL DEFAULT 0 COMMENT 'Lượt xem',
    `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_slug` (`slug`),
    KEY `idx_status` (`status`),
    KEY `idx_deadline` (`deadline`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Bảng quản lý đơn ứng tuyển
CREATE TABLE IF NOT EXISTS `applications`
(
    `id`             INT(11)      NOT NULL AUTO_INCREMENT,
    `recruitment_id` INT(11)      NOT NULL COMMENT 'ID bài tuyển dụng',
    `fullname`       VARCHAR(100) NOT NULL COMMENT 'Họ tên',
    `phone`          VARCHAR(20)  NOT NULL COMMENT 'Số điện thoại',
    `email`          VARCHAR(100) NOT NULL COMMENT 'Email',
    `content`        TEXT COMMENT 'Nội dung ứng tuyển',
    `cv_file`        VARCHAR(500)          DEFAULT NULL COMMENT 'Đường dẫn file CV',
    `status`         TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'Trạng thái: 0-Chờ xử lý, 1-Đã xem, 2-Phỏng vấn, 3-Trúng tuyển, 4-Từ chối',
    `notes`          TEXT COMMENT 'Ghi chú',
    `applied_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `processed_at`   TIMESTAMP    NULL     DEFAULT NULL,
    `ip_address`     VARCHAR(45)           DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_recruitment_id` (`recruitment_id`),
    KEY `idx_email` (`email`),
    KEY `idx_status` (`status`),
    FOREIGN KEY (`recruitment_id`) REFERENCES `recruitment_title` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Insert dữ liệu mẫu
INSERT INTO `recruitment_title` (`slug`, `title`, `position`, `description`, `requirements`, `benefits`,
                                 `work_location`,
                                 `degree`, `quantity`, `deadline`, `salary`, `contact_person`, `contact_phone`,
                                 `contact_email`, `image`)
VALUES ('truong-phong-nguon-von', 'Trưởng phòng nguồn vốn', 'Trưởng phòng',
        '<ul><li>Quản lý, theo dõi nhu cầu và cấp phát văn phòng phẩm, mực in, nước uống.</li><li>Thực hiện các thủ tục thanh toán chi phí hành chính văn phòng.</li><li>Soạn thảo, lưu trữ hồ sơ, giấy tờ liên quan.</li><li>Tổ chức sự kiện và truyền thông nội bộ.</li><li>Quản lý website, fanpage của công ty.</li><li>Thực hiện các công việc khác theo chỉ đạo của cấp trên.</li></ul>',
        '<ul><li>Tốt nghiệp đại học chuyên ngành Kinh tế, Tài chính, Ngân hàng hoặc các ngành liên quan.</li><li>Ưu tiên ứng viên có kinh nghiệm trong lĩnh vực tài chính, ngân hàng.</li><li>Có khả năng giao tiếp và thuyết trình tốt.</li><li>Tinh thần trách nhiệm cao, làm việc cẩn thận, tỉ mỉ.</li><li>Có khả năng làm việc độc lập và theo nhóm.</li></ul>',
        '<ul><li>Lương thỏa thuận: 7 - 10 triệu đồng/tháng.</li><li>Lương tháng 13 và thưởng các ngày Lễ, Tết theo quy định của công ty.</li><li>Tham gia BHYT, BHXH theo quy định của Nhà nước.</li><li>Du lịch, nghỉ mát hằng năm.</li><li>Được đào tạo kỹ năng chuyên môn, làm việc trong môi trường thân thiện, khuyến khích sáng tạo.</li></ul>',
        'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
        'Cao Đẳng - Đại Học',
        1,
        '2025-11-22',
        '7 - 10 triệu đồng/tháng',
        'Chị Phương – PHCNS',
        '0999 678 6789',
        'tuyendung@capitalam.vn',
        'img/recruitment/truong-phong-nguon-von-1763953822-egprx.webp'),

       ('chuyen-vien-dau-tu', 'Chuyên viên đầu tư', 'Chuyên viên',
        '<ul><li>Nghiên cứu và phân tích thị trường đầu tư</li><li>Đánh giá các dự án đầu tư tiềm năng</li><li>Lập báo cáo phân tích tài chính</li><li>Hỗ trợ quản lý danh mục đầu tư</li></ul>',
        '<ul><li>Tốt nghiệp đại học chuyên ngành Tài chính, Đầu tư, Kinh tế</li><li>Có kiến thức về phân tích tài chính</li><li>Thành thạo Excel và các công cụ phân tích</li></ul>',
        '<ul><li>Lương thỏa thuận: 10 - 15 triệu đồng/tháng</li><li>Thưởng theo hiệu suất công việc</li><li>BHXH, BHYT đầy đủ</li></ul>',
        'Hội sở: Hasco Building, 98 Xuân Thủy, Phường An Khánh, Tp. Hồ Chí Minh',
        'Đại học',
        1,
        '2025-11-22',
        '10 - 15 triệu đồng/tháng',
        'Chị Phương',
        '0999 678 6789',
        'tuyendung@capitalam.vn',
        'img/recruitment/truong-phong-nguon-von-1763953822-egprx.webp');
