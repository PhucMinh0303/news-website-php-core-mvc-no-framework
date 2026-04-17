-- Tạo database
CREATE DATABASE IF NOT EXISTS quanly_tintuc
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

CREATE USER IF NOT EXISTS 'capitalam'@'localhost' IDENTIFIED BY '123456';

-- Sử dụng database
USE quanly_tintuc;

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
VALUES (1,
        'NÂNG HẠNG - KHỞI ĐẦU CHO CÁC QUYẾT SÁCH, CẢI CÁCH MẠNH MẼ HƠN, CHUẨN MỰC HƠN VÀ KỶ LUẬT HƠN',
        'nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon',
        3,
        'Nguyễn Văn A',
        '2025-12-21',
        'img/section5/nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon-1763272692-veex8.webp',
        '<p>Nội dung chi tiết về nâng hạng và cải cách...</p>',
        'published',
        0),
       (2,
        'Nâng hạng - khởi đầu cho các quyết sách, cải cách mạnh mẽ hơn, chuẩn mực hơn và kỷ luật hơn',
        'nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon-2',
        3,
        'Nguyễn Văn A',
        '2025-12-21',
        'img/section5/nang-hang-khoi-dau-cho-cac-quyet-sach-cai-cach-manh-me-hon-chuan-muc-hon-va-ky-luat-hon-1763272625-jr69f.webp',
        '<p>Nội dung chi tiết về nâng hạng và cải cách (bài viết thứ 2)...</p>',
        'published',
        0),
       (3,
        'Đầu tư tài chính với số vốn nhỏ - Nên hay không?',
        'dau-tu-tai-chinh-voi-so-von-nho-nen-hay-khong',
        3,
        'Nguyễn Văn A',
        '2025-11-17',
        'img/news/dau-tu-tai-chinh-voi-so-von-nho-nen-hay-khong-1763350202-indak.webp',
        '<p>Trên thị trường hiện có nhiều hình thức đầu tư giúp gia tăng số tiền nhanh chóng. Tuy nhiên, để có được lợi nhuận cao thường đòi hỏi việc đầu tư, kinh doanh số vốn khá lớn. Do đó, rất nhiều người đặt ra câu hỏi "Có thể đầu tư tài chính với số vốn nhỏ không?"</p>',
        'published',
        0),
       (4,
        'Đầu tư tài chính 4.0 - Nhà đầu tư cần cẩn trọng với những chiêu trò lừa đảo!',
        'dau-tu-tai-chinh-4-0-nha-dau-tu-can-can-trong-voi-nhung-chieu-tro-lua-dao',
        3,
        'Nguyễn Văn A',
        '2025-11-17',
        'img/news/dau-tu-tai-chinh-4-0-nha-dau-tu-can-can-trong-voi-nhung-chieu-tro-lua-dao-1763350121-7qbwc.webp',
        '<p>Đầu tư tài chính là một lĩnh vực hấp dẫn và tiềm năng cho những ai muốn gia tăng thu nhập, khao khát đạt được tự do tài chính. Tuy nhiên, với sự phát triển của công nghệ và xu hướng đầu tư tài chính 4.0, cũng có nhiều trường hợp nhà đầu tư bị lừa đảo đầu tư tài chính thông qua những hình thức đầu tư online.</p>',
        'published',
        0),
       (5,
        'Các App đầu tư chứng khoán uy tín nhất trên thị trường năm 2025',
        'cac-app-dau-tu-chung-khoan-uy-tin-nhat-tren-thi-truong-nam-2025',
        3,
        'Nguyễn Văn A',
        '2025-11-17',
        'img/news/cac-app-dau-tu-chung-khoan-uy-tin-nhat-tren-thi-truong-nam-2025-1763349359-ctajx.webp',
        '<p>Với sự phát triển của thị trường chứng khoán Việt Nam, nhiều app đầu tư chứng khoán được phát triển giúp nhà đầu tư thuận tiện hơn trong việc giao dịch cổ phiếu trên điện thoại. Cùng Anfin điểm danh 9 app đầu tư chứng khoán uy tín hàng đầu trên thị trường Việt Nam, giúp nhà đầu tư yên tâm giao dịch.</p>',
        'published',
        0),
       (6,
        'Top 5 các diễn đàn đầu tư tài chính uy tín nhất hiện nay',
        'top-5-cac-dien-dan-dau-tu-tai-chinh-uy-tin-nhat-hien-nay',
        3,
        'Nguyễn Văn A',
        '2025-11-17',
        'img/news/top-5-cac-dien-dan-dau-tu-tai-chinh-uy-tin-nhat-hien-nay-1763349148-h1mp8.webp',
        '<p>Trên thị trường hiện nay có rất nhiều những diễn đàn đầu tư tài chính, tuy nhiên không phải nhà đầu tư nào cũng tìm được cho mình một diễn đàn đầu tư uy tín và chất lượng. Trong bài viết dưới đây, EMIR sẽ mang đến những thông tin hữu ích giúp quý nhà đầu tư có thể hiểu rõ hơn về diễn đàn đầu tư tài chính cũng như tìm được diễn đàn phù hợp nhất, hỗ trợ cho mình trong quá trình đầu tư.</p>',
        'published',
        0),
       (7,
        'Quản lý tài sản cá nhân - Nên bắt đầu từ đâu',
        'quan-ly-tai-san-ca-nhan-nen-bat-dau-tu-dau',
        3,
        'Nguyễn Văn A',
        '2025-11-17',
        'img/news/quan-ly-tai-san-ca-nhan-nen-bat-dau-tu-dau-1763348797-0ws4a.webp',
        '<p>Quản lý tài sản giúp cá nhân và tổ chức đảm bảo rằng họ sử dụng tài sản một cách hiệu quả và đạt được mục tiêu tài chính của mình. Song, việc quản lý tài sản là công việc tốn khá nhiều thời gian và đòi hỏi người quản lý phải có kiến thức cũng như kinh nghiệm. Nếu bạn chưa biết bắt đầu quản lý tài sản cá nhân như thế nào, tham khảo ngay bài viết dưới đây nhé!</p>',
        'published',
        0);

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
