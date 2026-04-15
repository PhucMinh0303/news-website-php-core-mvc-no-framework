<?php
require_once __DIR__ . '/../core/Database.php';

class RecruitmentModel
{
    private $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance();
    }

    /**
     * Tạo slug từ title
     */
    public function createSlug($string)
    {
        $string = preg_replace('/[^a-z0-9-]/', '-', strtolower(trim($string)));
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }

    /**
     * Lấy tất cả recruitments với phân trang và filter
     */
    public function getAll($status = null, $search = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM recruitments WHERE 1=1";
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR work_location LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = $limit;
        $params[':offset'] = $offset;

        return $this->conn->fetchAll($sql, $params);
    }

    /**
     * Đếm tổng số bản ghi
     */
    public function getTotal($status = null, $search = '')
    {
        $sql = "SELECT COUNT(*) as total FROM recruitments WHERE 1=1";
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR work_location LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $result = $this->conn->fetchOne($sql, $params);
        return $result['total'];
    }

    /**
     * Lấy recruitment theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM recruitments WHERE id = :id";
        $params = [':id' => $id];
        return $this->conn->fetchOne($sql, $params);
    }

    /**
     * Lấy recruitment theo slug
     */
    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM recruitments WHERE slug = :slug";
        $params = [':slug' => $slug];
        return $this->conn->fetchOne($sql, $params);
    }

    /**
     * Lấy các recruitment đang hoạt động (status = 1)
     */
    public function getActiveRecruitments($limit = 10, $offset = 0)
    {
        return $this->getAll(1, '', $limit, $offset);
    }

    /**
     * Đếm số recruitment đang hoạt động
     */
    public function countActive()
    {
        return $this->getTotal(1, '');
    }

    /**
     * Lấy recruitment nổi bật (có thể dùng views hoặc mới nhất)
     */
    public function getFeaturedRecruitments($limit = 5)
    {
        $sql = "SELECT * FROM recruitments WHERE status = 1 ORDER BY created_at DESC LIMIT :limit";
        $params = [':limit' => $limit];
        return $this->conn->fetchAll($sql, $params);
    }

    /**
     * Lấy recruitment theo vị trí (position/degree)
     */
    public function getByPosition($position, $limit = 10)
    {
        $sql = "SELECT * FROM recruitments WHERE status = 1 AND degree = :degree ORDER BY created_at DESC LIMIT :limit";
        $params = [':degree' => $position, ':limit' => $limit];
        return $this->conn->fetchAll($sql, $params);
    }

    /**
     * Tìm kiếm recruitment
     */
    public function search($keyword, $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM recruitments WHERE status = 1 AND (title LIKE :keyword OR work_location LIKE :keyword OR description LIKE :keyword) 
                ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params = [
            ':keyword' => "%{$keyword}%",
            ':limit' => $limit,
            ':offset' => $offset
        ];
        return $this->conn->fetchAll($sql, $params);
    }

    /**
     * Tăng lượt xem
     */
    public function incrementViews($id)
    {
        // Nếu có cột views, nếu không thì có thể bỏ qua hoặc thêm cột
        // $sql = "UPDATE recruitments SET views = views + 1 WHERE id = :id";
        // return $this->conn->update($sql, [':id' => $id]);
        return true;
    }

    /**
     * Thêm mới recruitment
     */
    public function create($data)
    {
        try {
            $slug = $this->createSlug($data['title']);

            // Kiểm tra slug đã tồn tại chưa
            $existing = $this->getBySlug($slug);
            if ($existing) {
                $slug = $slug . '-' . time();
            }

            $sql = "INSERT INTO recruitments (
                        title, slug, image, work_location, degree, quantity, 
                        salary_range, deadline, description, requirements, benefits, status
                    ) VALUES (
                        :title, :slug, :image, :work_location, :degree, :quantity,
                        :salary_range, :deadline, :description, :requirements, :benefits, :status
                    )";

            $params = [
                ':title' => $data['title'],
                ':slug' => $slug,
                ':image' => $data['image'] ?? 'default-job.webp',
                ':work_location' => $data['work_location'],
                ':degree' => $data['degree'],
                ':quantity' => $data['quantity'] ?? 1,
                ':salary_range' => $data['salary_range'] ?? null,
                ':deadline' => $data['deadline'],
                ':description' => $data['description'] ?? null,
                ':requirements' => $data['requirements'] ?? null,
                ':benefits' => $data['benefits'] ?? null,
                ':status' => $data['status'] ?? 1
            ];

            return $this->conn->insert($sql, $params);
        } catch (Exception $e) {
            error_log("Error creating recruitment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật recruitment
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE recruitments SET 
                        title = :title,
                        work_location = :work_location,
                        degree = :degree,
                        quantity = :quantity,
                        salary_range = :salary_range,
                        deadline = :deadline,
                        description = :description,
                        requirements = :requirements,
                        benefits = :benefits,
                        status = :status
                    WHERE id = :id";

            // Nếu có cập nhật ảnh
            if (isset($data['image']) && !empty($data['image'])) {
                $sql = str_replace("WHERE id = :id", "image = :image, WHERE id = :id", $sql);
                $params[':image'] = $data['image'];
            }

            $params = [
                ':id' => $id,
                ':title' => $data['title'],
                ':work_location' => $data['work_location'],
                ':degree' => $data['degree'],
                ':quantity' => $data['quantity'] ?? 1,
                ':salary_range' => $data['salary_range'] ?? null,
                ':deadline' => $data['deadline'],
                ':description' => $data['description'] ?? null,
                ':requirements' => $data['requirements'] ?? null,
                ':benefits' => $data['benefits'] ?? null,
                ':status' => $data['status'] ?? 1
            ];

            return $this->conn->update($sql, $params);
        } catch (Exception $e) {
            error_log("Error updating recruitment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa recruitment
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM recruitments WHERE id = :id";
            $params = [':id' => $id];
            return $this->conn->delete($sql, $params);
        } catch (Exception $e) {
            error_log("Error deleting recruitment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật status
     */
    public function updateStatus($id, $status)
    {
        try {
            $sql = "UPDATE recruitments SET status = :status WHERE id = :id";
            $params = [':status' => $status, ':id' => $id];
            return $this->conn->update($sql, $params);
        } catch (Exception $e) {
            error_log("Error updating status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả vị trí (degree) khác nhau
     */
    public function getAllPositions()
    {
        $sql = "SELECT DISTINCT degree FROM recruitments WHERE status = 1 ORDER BY degree";
        $results = $this->conn->fetchAll($sql);
        return array_column($results, 'degree');
    }

    /**
     * Upload ảnh
     */
    public function uploadImage($file)
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return 'default-job.webp';
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            return 'default-job.webp';
        }

        // Tạo tên file unique
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . '/capitalam2-mvc/public/uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $filename;
        }

        return 'default-job.webp';
    }
}

?>