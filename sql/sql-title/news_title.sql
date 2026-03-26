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

-- Thêm dữ liệu chi tiết cho bài viết "Đầu tư tài chính với số vốn nhỏ - Nên hay không?" (news_id = 3)
INSERT INTO news_title (news_id,
                        title,
                        slug,
                        description,
                        content,
                        meta_title,
                        meta_description,
                        meta_keywords,
                        featured_image,
                        featured_image_caption,
                        video_url,
                        audio_url,
                        gallery_images,
                        source,
                        source_url,
                        author_note,
                        reading_time,
                        is_featured,
                        is_breaking,
                        is_hot,
                        views,
                        share_count,
                        like_count,
                        comment_count,
                        status,
                        published_at,
                        scheduled_at)
VALUES (3,
        'Đầu tư tài chính với số vốn nhỏ - Nên hay không?',
        'dau-tu-tai-chinh-voi-so-von-nho-nen-hay-khong',
        'Trên thị trường hiện có nhiều hình thức đầu tư giúp gia tăng số tiền nhanh chóng. Tuy nhiên, để có được lợi nhuận cao thường đòi hỏi việc đầu tư, kinh doanh số vốn khá lớn. Do đó, rất nhiều người đặt ra câu hỏi "Có thể đầu tư tài chính với số vốn nhỏ không?"',
        '<div class="f-detail clearfix">
            <img alt="" src="https://demo29.escovietnam.vn/capitalAM/uploads/noidung/images/baiviet/unnamed-18.png" style="width: 100%" />

            <p>Trên thị trường hiện có nhiều hình thức đầu tư giúp gia tăng số tiền nhanh chóng. Tuy nhiên, để có được lợi nhuận cao thường đòi hỏi việc đầu tư, kinh doanh số vốn khá lớn. Do đó, rất nhiều người đặt ra câu hỏi "Có thể đầu tư tài chính với số vốn nhỏ không?"</p>

            <ol>
                <li><strong>Có nên đầu tư tài chính với số vốn nhỏ không?</strong></li>
            </ol>

            <p>Bất kỳ nhà đầu tư tên tuổi nào đều khởi đầu bằng việc đầu tư với số vốn khiêm tốn. Đây là những bước đầu tiên để họ học hỏi kinh nghiệm, tiếp thu kiến thức đầu tư trước khi tiến xa hơn trên con đường đầu tư tài chính với những khoản đầu tư lớn hơn.</p>

            <p>Hãy ghi nhớ, hiệu quả của đầu tư sẽ không bị ảnh hưởng quá nhiều bởi khoản vốn bạn bỏ ra, nó sẽ phụ thuộc vào thời gian bạn dành cho việc đầu tư, lĩnh vực bạn quan tâm và tỷ lệ lợi nhuận bạn có thể đạt được…</p>

            <p>Việc đầu tư tài chính với số vốn nhỏ mang đến nhiều lợi ích cho nhà đầu tư:</p>

            <ul>
                <li>Có nhiều ngành nghề, lĩnh vực để nhà đầu tư vốn nhỏ có thể cân nhắc: gửi tiết kiệm, bảo hiểm đầu tư, cổ phiếu, trái phiếu hay tiền ảo…</li>
                <li>Rủi ro nếu có sẽ không quá lớn làm ảnh hưởng tới cuộc sống cũng như công việc của nhà đầu tư</li>
                <li>Có cơ hội học hỏi, rèn luyện tư duy, kiến thức tài chính, chuẩn bị cho những dự định đầu tư vốn lớn trong tương lai khi có đủ tiềm lực</li>
            </ul>

            <ol start="2">
                <li><strong>Vốn nhỏ nên đầu tư gì?</strong></li>
            </ol>

            <p><img alt="" src="https://demo29.escovietnam.vn/capitalAM/uploads/noidung/images/baiviet/unnamed-18.png" style="width: 100%" /></p>
            <p><em>Nên đầu tư gì khi đầu tư tài chính với số vốn nhỏ</em></p>

            <p>Việc để dành ra một khoản tích lũy nhỏ mỗi ngày sẽ mang đến cho bạn khoản tiền đáng kể trong tương lai. Do đó, thay vì lo lắng, hãy tập tích lũy và đầu tư ngay khi bạn có dự định. Một số kênh đầu tư vốn nhỏ bạn có thể tham khảo:</p>

            <ul>
                <li><strong>Gửi tích lũy</strong></li>
            </ul>

            <p>Gửi tích lũy là phương pháp an toàn, mang lại lợi tức ổn định cho nhà đầu tư. Nếu như trước đây mọi người thường gửi tiết kiệm vào ngân hàng thì nay có thể tích lũy/ gửi tiết kiệm online thông qua các ứng dụng đầu tư tài chính trực tuyến. Việc mở tài khoản tích lũy trên các ứng dụng đầu tư khá đơn giản, lợi tức cao hơn lãi suất ngân hàng và thường có những ưu đãi cho khách hàng theo từng đợt. Một trong những ứng dụng tích lũy online bạn có thể tham khảo đó là EPIC CENTER, Finhay…</p>

            <p>Những ứng dụng này cho phép nhà đầu tư tích lũy chỉ từ 50.000VNĐ, đây là mức đầu tư phù hợp cho những nhà đầu tư mới tham gia vào thị trường tài chính. Đặc biệt, các ứng dụng này thường miễn phí chi phí nạp – rút tiền cho người dùng.</p>

            <ul>
                <li><strong>Đầu tư vào chứng chỉ quỹ</strong></li>
            </ul>

            <p>Với khả năng sinh lời tốt, chứng chỉ quỹ là hình thức đầu tư được nhiều nhà đầu tư lựa chọn. Nhà đầu tư sẽ không có quyền trực tiếp đưa ra quyết định của mình trong quá trình đầu tư. Với hình thức đầu tư vào chứng chỉ quỹ, nhà đầu tư sẽ ít bị ảnh hưởng bởi biến động thị trường, tính thanh khoản tương đối cao và có thể rút tiền bất cứ lúc nào.</p>

            <ul>
                <li><strong>Đầu tư cổ phiếu</strong></li>
            </ul>

            <p>Cổ phiếu là hình thức đầu tư được nhiều nhà đầu tư ưa thích bởi mang lại nguồn lợi nhuận lớn. Tuy nhiên, nhà đầu tư cần phải có kiến thức và kỹ năng về thị trường tài chính trước khi bước chân vào lĩnh vực đầu tư này.</p>

            <p>Cổ phiếu có tính thanh khoản cao nên bạn có thể dễ dàng thực hiện giao dịch mua bán để thu hồi vốn mọi thời điểm. Số vốn để đầu tư cổ phiếu chỉ từ 1 – 2 triệu đồng, được cho là không quá lớn.</p>

            <ol start="3">
                <li><strong>Đầu tư bất động sản với số vốn nhỏ cùng EMIR</strong></li>
            </ol>

            <p>Đầu tư bất động sản được coi là hình thức đầu tư cần huy động số vốn lớn lên đến hàng trăm thậm chí hàng tỷ đồng. Tuy nhiên, nhà đầu vốn nhỏ hiện nay vẫn có thể đầu tư bất động sản hiệu quả nếu đầu tư thông qua EMIR.</p>

            <p>Những dự án bất động sản của EMIR cho phép đầu tư với số tiền chỉ từ 10 triệu đồng, một mức đầu tư không tưởng cho một dự án bất động sản hạng sang.</p>

            <p>Một trong số những sản phẩm đầu tư bất động sản uy tín của EMIR có thể kể đến là dự án Nam Đông Phát – Thanh Hóa.</p>

            <p>Dự án có mục tiêu hiện thực hóa quy hoạch chi tiết xây dựng tỷ lệ 1/500 đã được phê duyệt; hình thành khu ở mới, đáp ứng nhu cầu về nhà ở, sinh hoạt của người dân trong khu vực, đồng thời xây dựng cơ sở hạ tầng đồng bộ; góp phần thúc đẩy phát triển kinh tế – xã hội của địa phương.</p>

            <p><img alt="" src="https://demo29.escovietnam.vn/capitalAM/uploads/noidung/images/baiviet/unnamed-1.png" style="width: 100%" /></p>
            <p><em>Dự án Nam Đông Phát – Sản phẩm hợp tác đầu tư tiềm năng của EMIR</em></p>

            <p>Nam Đông Phát hứa hẹn tiềm năng sinh lời vô hạn nhờ khả năng phát triển đô thị trong tương lai khi sở hữu hệ tiện ích ngoại khu đa dạng: trường học Quốc tế liên cấp Newton, Công viên cây xanh rộng: 8.358m², Sân thể dục thể thao: 34.967m², Nhà văn hóa: 2.100m²…</p>

            <p>Cùng khả năng liên kết vùng hoàn hảo khi thuộc khu đô thị phía Đông Nam thành phố, chỉ cách trung tâm thành phố 3km, cách chợ đầu mối 1km, cách trường Đại học Hồng Đức, Đại học Văn Hóa Nghệ Thuật, các khu bệnh viện đa khoa và bệnh viện Nhi 1km…</p>

            <p>Nếu bạn đang muốn đầu tư tài chính với số vốn nhỏ, bạn có thể tham khảo những kênh đầu tư kể trên, đặc biệt là dự án bất động sản Nam Đông Phát của EMIR. Để tìm hiểu thêm những kênh đầu tư tài chính hiệu quả, những sản phẩm đầu tư uy tín, quý khách vui lòng liên hệ với EMIR qua những kênh thông tin chính thống.</p>

            <p>Thông tin chi tiết:</p>

            <p>Trụ sở: Tầng 1+2, Tòa nhà Lâm Viên Complex, Số 107A Nguyễn Phong Sắc, Phường Dịch Vọng Hậu, Quận Cầu Giấy, Thành phố Hà Nội, Việt Nam</p>

            <p>Website: Emir.vn</p>

            <p>Fanpage: Emir – The Elite Advisors</p>
        </div>',
        'Đầu tư tài chính với số vốn nhỏ - Nên hay không? | EMIR',
        'Tìm hiểu có nên đầu tư tài chính với số vốn nhỏ? Khám phá các kênh đầu tư hiệu quả như gửi tích lũy, chứng chỉ quỹ, cổ phiếu và dự án bất động sản cùng EMIR.',
        'đầu tư tài chính, đầu tư vốn nhỏ, đầu tư tài chính vốn nhỏ, đầu tư bất động sản, EMIR, gửi tích lũy, chứng chỉ quỹ, đầu tư cổ phiếu',
        'img/news/dau-tu-tai-chinh-voi-so-von-nho-nen-hay-khong-1763350202-indak.webp',
        'Đầu tư tài chính với số vốn nhỏ - Nên hay không?',
        NULL,
        NULL,
        NULL,
        'EMIR',
        'https://emir.vn',
        NULL,
        12,
        FALSE,
        FALSE,
        TRUE,
        8,
        0,
        0,
        0,
        'published',
        '2025-11-17 10:30:00',
        NULL);

-- Thêm dữ liệu chi tiết cho các bài viết khác (nếu có)
-- Bài viết ID 1: NÂNG HẠNG - KHỞI ĐẦU CHO CÁC QUYẾT SÁCH...
INSERT INTO news_title (news_id,
                        title,
                        slug,
                        description,
                        content,
                        meta_title,
                        meta_description,
                        featured_image,
                        reading_time,
                        is_featured,
                        is_breaking,
                        is_hot,
                        status,
                        published_at)
VALUES (1,
        'NÂNG HẠNG - KHỞI ĐẦU CHO CÁC QUYẾT SÁCH, CẢI CÁCH MẠNH MẼ HƠN, CHUẨN MỰC HƠN VÀ KỶ LUẬT HƠN',
        'nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon',
        'Nâng hạng là bước khởi đầu quan trọng cho các quyết sách cải cách mạnh mẽ, chuẩn mực và kỷ luật hơn trong quản trị doanh nghiệp và đầu tư tài chính.',
        '<p>Nội dung chi tiết về nâng hạng và cải cách trong quản trị doanh nghiệp...</p>',
        'Nâng hạng - Khởi đầu cho cải cách mạnh mẽ | EMIR',
        'Bài viết phân tích về tầm quan trọng của việc nâng hạng trong quản trị doanh nghiệp và đầu tư tài chính.',
        'img/section5/nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon-1763272692-veex8.webp',
        8,
        TRUE,
        FALSE,
        TRUE,
        'published',
        '2025-12-21 08:00:00');

-- Bài viết ID 2: Nâng hạng - khởi đầu...
INSERT INTO news_title (news_id,
                        title,
                        slug,
                        description,
                        content,
                        meta_title,
                        meta_description,
                        featured_image,
                        reading_time,
                        is_featured,
                        is_breaking,
                        is_hot,
                        status,
                        published_at)
VALUES (2,
        'Nâng hạng - khởi đầu cho các quyết sách, cải cách mạnh mẽ hơn, chuẩn mực hơn và kỷ luật hơn',
        'nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon-2',
        'Bài viết tiếp nối về chủ đề nâng hạng và cải cách trong hệ thống tài chính doanh nghiệp.',
        '<p>Nội dung chi tiết về nâng hạng và cải cách (bài viết thứ 2)...</p>',
        'Nâng hạng - Cải cách hệ thống tài chính | EMIR',
        'Những cải cách mạnh mẽ trong hệ thống tài chính và quản trị doanh nghiệp.',
        'img/section5/nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon-1763272625-jr69f.webp',
        7,
        FALSE,
        FALSE,
        FALSE,
        'published',
        '2025-12-21 10:00:00');

-- Bài viết ID 4: Đầu tư tài chính 4.0 - Nhà đầu tư cần cẩn trọng...
INSERT INTO news_title (news_id,
                        title,
                        slug,
                        description,
                        content,
                        meta_title,
                        meta_description,
                        featured_image,
                        reading_time,
                        is_featured,
                        is_breaking,
                        is_hot,
                        status,
                        published_at)
VALUES (4,
        'Đầu tư tài chính 4.0 - Nhà đầu tư cần cẩn trọng với những chiêu trò lừa đảo!',
        'dau-tu-tai-chinh-4-0-nha-dau-tu-can-can-trong-voi-nhung-chieu-tro-lua-dao',
        'Cảnh báo về các chiêu trò lừa đảo trong đầu tư tài chính 4.0 và cách phòng tránh cho nhà đầu tư.',
        '<p>Đầu tư tài chính là một lĩnh vực hấp dẫn và tiềm năng. Tuy nhiên, với sự phát triển của công nghệ và xu hướng đầu tư tài chính 4.0, cũng có nhiều trường hợp nhà đầu tư bị lừa đảo...</p>',
        'Cảnh báo lừa đảo đầu tư tài chính 4.0 | EMIR',
        'Cảnh báo các chiêu trò lừa đảo trong đầu tư tài chính 4.0 và hướng dẫn cách phòng tránh cho nhà đầu tư.',
        'img/news/dau-tu-tai-chinh-4-0-nha-dau-tu-can-can-trong-voi-nhung-chieu-tro-lua-dao-1763350121-7qbwc.webp',
        10,
        TRUE,
        TRUE,
        TRUE,
        'published',
        '2025-11-17 09:00:00');

-- Bài viết ID 5: Các App đầu tư chứng khoán uy tín nhất...
INSERT INTO news_title (news_id,
                        title,
                        slug,
                        description,
                        content,
                        meta_title,
                        meta_description,
                        featured_image,
                        reading_time,
                        is_featured,
                        is_breaking,
                        is_hot,
                        status,
                        published_at)
VALUES (5,
        'Các App đầu tư chứng khoán uy tín nhất trên thị trường năm 2025',
        'cac-app-dau-tu-chung-khoan-uy-tin-nhat-tren-thi-truong-nam-2025',
        'Tổng hợp 9 app đầu tư chứng khoán uy tín hàng đầu tại Việt Nam năm 2025, giúp nhà đầu tư giao dịch an toàn và hiệu quả.',
        '<p>Với sự phát triển của thị trường chứng khoán Việt Nam, nhiều app đầu tư chứng khoán được phát triển giúp nhà đầu tư thuận tiện hơn trong việc giao dịch cổ phiếu trên điện thoại...</p>',
        'Top 9 app đầu tư chứng khoán uy tín 2025 | EMIR',
        'Đánh giá và so sánh các ứng dụng đầu tư chứng khoán uy tín nhất tại Việt Nam năm 2025.',
        'img/news/cac-app-dau-tu-chung-khoan-uy-tin-nhat-tren-thi-truong-nam-2025-1763349359-ctajx.webp',
        9,
        TRUE,
        FALSE,
        TRUE,
        'published',
        '2025-11-17 08:30:00');

-- Bài viết ID 6: Top 5 các diễn đàn đầu tư tài chính uy tín...
INSERT INTO news_title (news_id,
                        title,
                        slug,
                        description,
                        content,
                        meta_title,
                        meta_description,
                        featured_image,
                        reading_time,
                        is_featured,
                        is_breaking,
                        is_hot,
                        status,
                        published_at)
VALUES (6,
        'Top 5 các diễn đàn đầu tư tài chính uy tín nhất hiện nay',
        'top-5-cac-dien-dan-dau-tu-tai-chinh-uy-tin-nhat-hien-nay',
        'Giới thiệu top 5 diễn đàn đầu tư tài chính uy tín và chất lượng, nơi nhà đầu tư có thể học hỏi kinh nghiệm và cập nhật thông tin.',
        '<p>Trên thị trường hiện nay có rất nhiều những diễn đàn đầu tư tài chính, tuy nhiên không phải nhà đầu tư nào cũng tìm được cho mình một diễn đàn đầu tư uy tín và chất lượng...</p>',
        'Top 5 diễn đàn đầu tư tài chính uy tín | EMIR',
        'Danh sách các diễn đàn đầu tư tài chính uy tín, nơi nhà đầu tư có thể học hỏi và chia sẻ kinh nghiệm.',
        'img/news/top-5-cac-dien-dan-dau-tu-tai-chinh-uy-tin-nhat-hien-nay-1763349148-h1mp8.webp',
        8,
        FALSE,
        FALSE,
        FALSE,
        'published',
        '2025-11-17 08:00:00');

-- Bài viết ID 7: Quản lý tài sản cá nhân - Nên bắt đầu từ đâu
INSERT INTO news_title (news_id,
                        title,
                        slug,
                        description,
                        content,
                        meta_title,
                        meta_description,
                        featured_image,
                        reading_time,
                        is_featured,
                        is_breaking,
                        is_hot,
                        status,
                        published_at)
VALUES (7,
        'Quản lý tài sản cá nhân - Nên bắt đầu từ đâu',
        'quan-ly-tai-san-ca-nhan-nen-bat-dau-tu-dau',
        'Hướng dẫn chi tiết cách quản lý tài sản cá nhân hiệu quả, từ những bước cơ bản đến chiến lược đầu tư dài hạn.',
        '<p>Quản lý tài sản giúp cá nhân và tổ chức đảm bảo rằng họ sử dụng tài sản một cách hiệu quả và đạt được mục tiêu tài chính của mình...</p>',
        'Hướng dẫn quản lý tài sản cá nhân hiệu quả | EMIR',
        'Bài viết hướng dẫn chi tiết cách quản lý tài sản cá nhân, từ việc lập kế hoạch đến các chiến lược đầu tư phù hợp.',
        'img/news/quan-ly-tai-san-ca-nhan-nen-bat-dau-tu-dau-1763348797-0ws4a.webp',
        11,
        TRUE,
        FALSE,
        TRUE,
        'published',
        '2025-11-17 07:30:00');

-- Cập nhật views từ bảng news_title vào bảng news (nếu cần đồng bộ)
UPDATE news n
    INNER JOIN news_title nt ON n.id = nt.news_id
SET n.views = nt.views
WHERE n.id = nt.news_id;


-- Tạo index cho news_title
CREATE INDEX idx_news_title_slug ON news_title (slug);
CREATE INDEX idx_news_title_status ON news_title (status);
CREATE INDEX idx_news_title_published ON news_title (published_at);
CREATE INDEX idx_news_title_featured ON news_title (is_featured);
CREATE INDEX idx_news_title_breaking ON news_title (is_breaking);
CREATE INDEX idx_news_title_hot ON news_title (is_hot);
CREATE FULLTEXT INDEX idx_news_title_search ON news_title (title, description, content);