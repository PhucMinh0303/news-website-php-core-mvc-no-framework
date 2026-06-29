-- =====================================================
-- 3. Bảng news (Tin tức)
-- =====================================================
DROP TABLE IF EXISTS news;
CREATE TABLE news
(
    news_id           INT PRIMARY KEY AUTO_INCREMENT,
    news_title        VARCHAR(255)        NOT NULL                                    COMMENT 'Tiêu đề tin tức',
    news_slug         VARCHAR(255) UNIQUE NOT NULL                                    COMMENT 'Đường dẫn URL (slug)',
    category_id  INT                                                             COMMENT 'ID danh mục tin tức',
    category     ENUM(
        'Chính trị & Xã hội',
        'Kinh tế',
        'Pháp luật',
        'Văn hóa & Giải trí',
        'Thể thao',
        'Công nghệ & Khoa học',
        'Đời sống/Gia đình'
    )                                                                            COMMENT 'Tên danh mục tin tức',
    author       VARCHAR(100)        NOT NULL                                    COMMENT 'Tên tác giả',
    publish_date DATE                NOT NULL                                    COMMENT 'Ngày đăng tin',
    image        VARCHAR(255)                                                    COMMENT 'Hình ảnh đại diện bài viết',
    avatar_img   VARCHAR(255)                                                    COMMENT 'Ảnh đại diện cho tin tức trong admin',
    video        VARCHAR(255)                                                    COMMENT 'Đường dẫn video (nếu có)',
    content      LONGTEXT            NOT NULL                                    COMMENT 'Nội dung bài viết (hỗ trợ HTML)',
    views        INT                                     DEFAULT 0               COMMENT 'Số lượt xem',
    status       ENUM('draft', 'published', 'archived') DEFAULT 'draft'         COMMENT 'Trạng thái: draft-Bản nháp, published-Đã đăng, archived-Lưu trữ',
    created_at   TIMESTAMP                               DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    updated_at   TIMESTAMP                               DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật',
    FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- =====================================================
-- Dữ liệu mẫu cho bảng news
-- =====================================================
INSERT INTO news (news_id, news_title, news_slug, category_id, category, author, publish_date, image, avatar_img, video, content, status, views)
VALUES
    (
        1,
        'Nâng hạng - Khởi đầu cho các quyết sách, cải cách mạnh mẽ hơn, chuẩn mực hơn và kỷ luật hơn',
        'nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon',
        1,
        'Chính trị & Xã hội',
        'Nguyễn Văn A',
        '2025-12-21',
        'img/news/nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon.webp',
        'img/avatar/news-avatar-1.jpg',
        NULL,
        '<p>Nội dung chi tiết về nâng hạng và cải cách... Nâng hạng thị trường chứng khoán là một trong những mục tiêu quan trọng của Chính phủ, góp phần thu hút dòng vốn ngoại và nâng cao vị thế của Việt Nam trên trường quốc tế.</p>',
        'published',
        0
    ),
    (
        2,
        'Đầu tư tài chính với số vốn nhỏ - Nên hay không?',
        'dau-tu-tai-chinh-voi-so-von-nho-nen-hay-khong',
        2,
        'Kinh tế',
        'Trần Thị B',
        '2025-11-17',
        'img/news/dau-tu-tai-chinh-voi-so-von-nho-nen-hay-khong.webp',
        'img/avatar/news-avatar-2.jpg',
        'https://www.youtube.com/embed/example1',
        '<p>Trên thị trường hiện có nhiều hình thức đầu tư giúp gia tăng số tiền nhanh chóng. Tuy nhiên, để có được lợi nhuận cao thường đòi hỏi việc đầu tư, kinh doanh số vốn khá lớn. Do đó, rất nhiều người đặt ra câu hỏi "Có thể đầu tư tài chính với số vốn nhỏ không?"</p><p>Câu trả lời là CÓ. Với sự phát triển của công nghệ, nhà đầu tư có thể tham gia vào các kênh đầu tư với số vốn tối thiểu như: chứng chỉ quỹ, cổ phiếu, trái phiếu, hoặc các nền tảng đầu tư trực tuyến.</p>',
        'published',
        150
    ),
    (
        3,
        'Đầu tư tài chính 4.0 - Nhà đầu tư cần cẩn trọng với những chiêu trò lừa đảo!',
        'dau-tu-tai-chinh-4-0-nha-dau-tu-can-can-trong-voi-nhung-chieu-tro-lua-dao',
        2,
        'Kinh tế',
        'Lê Văn C',
        '2025-11-17',
        'img/news/dau-tu-tai-chinh-4-0-nha-dau-tu-can-can-trong-voi-nhung-chieu-tro-lua-dao.webp',
        'img/avatar/news-avatar-3.jpg',
        'https://www.youtube.com/embed/example2',
        '<p>Đầu tư tài chính là một lĩnh vực hấp dẫn và tiềm năng cho những ai muốn gia tăng thu nhập, khao khát đạt được tự do tài chính. Tuy nhiên, với sự phát triển của công nghệ và xu hướng đầu tư tài chính 4.0, cũng có nhiều trường hợp nhà đầu tư bị lừa đảo đầu tư tài chính thông qua những hình thức đầu tư online.</p><p>Các nhà đầu tư cần cảnh giác với các chiêu trò như: hứa hẹn lợi nhuận siêu cao, giả mạo sàn giao dịch, sử dụng phần mềm giao dịch ảo, hoặc yêu cầu chuyển tiền vào tài khoản cá nhân.</p>',
        'published',
        89
    ),
    (
        4,
        'Các App đầu tư chứng khoán uy tín nhất trên thị trường năm 2025',
        'cac-app-dau-tu-chung-khoan-uy-tin-nhat-tren-thi-truong-nam-2025',
        6,
        'Công nghệ & Khoa học',
        'Phạm Thị D',
        '2025-11-17',
        'img/news/cac-app-dau-tu-chung-khoan-uy-tin-nhat-tren-thi-truong-nam-2025.webp',
        'img/avatar/news-avatar-4.jpg',
        NULL,
        '<p>Với sự phát triển của thị trường chứng khoán Việt Nam, nhiều app đầu tư chứng khoán được phát triển giúp nhà đầu tư thuận tiện hơn trong việc giao dịch cổ phiếu trên điện thoại. Cùng điểm danh 9 app đầu tư chứng khoán uy tín hàng đầu trên thị trường Việt Nam, giúp nhà đầu tư yên tâm giao dịch.</p><p>Các ứng dụng được đánh giá cao bao gồm: SSI, VPS, HSC, MBS, VCBS, FPT Securities, KB Vietnam, TCBS và Mirae Asset.</p>',
        'published',
        234
    ),
    (
        5,
        'Top 5 các diễn đàn đầu tư tài chính uy tín nhất hiện nay',
        'top-5-cac-dien-dan-dau-tu-tai-chinh-uy-tin-nhat-hien-nay',
        2,
        'Kinh tế',
        'Hoàng Văn E',
        '2025-11-17',
        'img/news/top-5-cac-dien-dan-dau-tu-tai-chinh-uy-tin-nhat-hien-nay.webp',
        'img/avatar/news-avatar-5.jpg',
        NULL,
        '<p>Trên thị trường hiện nay có rất nhiều những diễn đàn đầu tư tài chính, tuy nhiên không phải nhà đầu tư nào cũng tìm được cho mình một diễn đàn đầu tư uy tín và chất lượng. Trong bài viết dưới đây, chúng tôi sẽ mang đến những thông tin hữu ích giúp quý nhà đầu tư có thể hiểu rõ hơn về diễn đàn đầu tư tài chính cũng như tìm được diễn đàn phù hợp nhất, hỗ trợ cho mình trong quá trình đầu tư.</p>',
        'published',
        67
    ),
    (
        6,
        'Quản lý tài sản cá nhân - Nên bắt đầu từ đâu',
        'quan-ly-tai-san-ca-nhan-nen-bat-dau-tu-dau',
        7,
        'Đời sống/Gia đình',
        'Nguyễn Thị F',
        '2025-11-17',
        'img/news/quan-ly-tai-san-ca-nhan-nen-bat-dau-tu-dau.webp',
        'img/avatar/news-avatar-6.jpg',
        'https://www.youtube.com/embed/example3',
        '<p>Quản lý tài sản giúp cá nhân và tổ chức đảm bảo rằng họ sử dụng tài sản một cách hiệu quả và đạt được mục tiêu tài chính của mình. Song, việc quản lý tài sản là công việc tốn khá nhiều thời gian và đòi hỏi người quản lý phải có kiến thức cũng như kinh nghiệm. Nếu bạn chưa biết bắt đầu quản lý tài sản cá nhân như thế nào, tham khảo ngay bài viết dưới đây nhé!</p>',
        'published',
        45
    ),
    (
        7,
        'Bài viết về Pháp luật - Những điểm mới trong Bộ luật Hình sự 2025',
        'bai-viet-ve-phap-luat-nhung-diem-moi-trong-bo-luat-hinh-su-2025',
        3,
        'Pháp luật',
        'Trần Văn G',
        '2025-11-16',
        'img/news/phap-luat-bai-viet-mau.webp',
        'img/avatar/news-avatar-7.jpg',
        NULL,
        '<p>Bộ luật Hình sự 2025 có nhiều điểm mới đáng chú ý, đặc biệt là các quy định liên quan đến tội phạm công nghệ cao, tội phạm môi trường và tội phạm trong lĩnh vực tài chính, ngân hàng.</p>',
        'draft',
        0
    ),
    (
        8,
        'Văn hóa & Giải trí: Xu hướng âm nhạc Việt Nam 2025',
        'van-hoa-giai-tri-xu-huong-am-nhac-viet-nam-2025',
        4,
        'Văn hóa & Giải trí',
        'Lê Thị H',
        '2025-11-15',
        'img/news/van-hoa-giai-tri-xu-huong-am-nhac.webp',
        'img/avatar/news-avatar-8.jpg',
        'https://www.youtube.com/embed/example4',
        '<p>Âm nhạc Việt Nam 2025 chứng kiến sự bùng nổ của các thể loại nhạc mới như: Pop Indie, EDM pha trộn với nhạc dân tộc, và sự trở lại của nhạc acoustic với phong cách hiện đại.</p>',
        'archived',
        23
    ),
    (
        9,
        'Thể thao: Vòng loại World Cup 2026 và cơ hội của đội tuyển Việt Nam',
        'the-thao-vong-loai-world-cup-2026-va-co-hoi-cua-doi-tuyen-viet-nam',
        5,
        'Thể thao',
        'Nguyễn Văn K',
        '2025-11-14',
        'img/news/the-thao-world-cup-2026.webp',
        'img/avatar/news-avatar-9.jpg',
        'https://www.youtube.com/embed/example5',
        '<p>Đội tuyển Việt Nam đang chuẩn bị cho vòng loại World Cup 2026 khu vực châu Á. Với sự chuẩn bị kỹ lưỡng cùng lối chơi phòng ngự phản công sắc sảo, thầy trò HLV Kim Sang-sik đang đặt mục tiêu giành vé vào vòng tiếp theo.</p>',
        'published',
        512
    ),
    (
        10,
        'Công nghệ 2025: Xu hướng AI và Blockchain bùng nổ',
        'cong-nghe-2025-xu-huong-ai-va-blockchain-bung-no',
        6,
        'Công nghệ & Khoa học',
        'Phạm Văn L',
        '2025-11-13',
        'img/news/cong-nghe-ai-blockchain-2025.webp',
        'img/avatar/news-avatar-10.jpg',
        'https://www.youtube.com/embed/example6',
        '<p>Năm 2025 đánh dấu sự bùng nổ của trí tuệ nhân tạo (AI) và công nghệ chuỗi khối (Blockchain) trong nhiều lĩnh vực như tài chính, y tế, giáo dục và giải trí. Các ứng dụng AI thế hệ mới với khả năng xử lý ngôn ngữ tự nhiên và ra quyết định thông minh đang thay đổi cách chúng ta sống và làm việc.</p>',
        'published',
        678
    );

-- =====================================================
-- Tạo index cho bảng news
-- =====================================================
CREATE INDEX idx_news_publish_date ON news (publish_date);
CREATE INDEX idx_news_category_id ON news (category_id);
CREATE INDEX idx_news_category ON news (category);
CREATE INDEX idx_news_status ON news (status);
CREATE INDEX idx_news_publish_date_status ON news (publish_date, status);