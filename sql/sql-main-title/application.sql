-- Tạo bảng ung_tuyen để lưu thông tin ứng tuyển
CREATE TABLE Application (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(100) NOT NULL,
    dien_thoai VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    noi_dung TEXT,
    file_pdf VARCHAR(255) NOT NULL,
    ngay_ung_tuyen TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Thêm dữ liệu mẫu vào bảng ung_tuyen
INSERT INTO Application (ho_ten, dien_thoai, email, noi_dung, file_pdf)
VALUES
('Nguyễn Văn A', '0987654321', 'nguyenvana@example.com', 'Tôi có 3 năm kinh nghiệm lập trình PHP.', 'uploads/cv/cv_nguyenvana.pdf'),
('Trần Thị B', '0912345678', 'tranthib@example.com', 'Tôi thành thạo MySQL và quản lý dữ liệu.', 'uploads/cv/cv_tranthib.pdf');

-- Truy vấn dữ liệu từ bảng ung_tuyen để hiển thị danh sách ứng viên
SELECT id, ho_ten, email, dien_thoai, noi_dung, file_pdf, ngay_ung_tuyen
FROM ung_tuyen
ORDER BY ngay_ung_tuyen DESC;

-- Cập nhật thông tin ứng viên
UPDATE Application

-- Truy vấn một bản ghi cụ thể theo ID
SELECT * FROM ung_tuyen WHERE id = 1;

-- Xóa một bản ghi ứng tuyển
DELETE FROM ung_tuyen WHERE id = 1;