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