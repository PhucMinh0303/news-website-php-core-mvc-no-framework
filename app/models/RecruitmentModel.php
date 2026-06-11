<?php
// models/RecruitmentModel.php
require_once __DIR__ . '/../core/Model.php';

class RecruitmentModel extends Model
{
    protected $table = 'recruitments';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lấy tất cả tin tuyển dụng với phân trang và filter (Admin)
     */
    public function getAllAdmin($status = null, $limit = null, $offset = 0, $search = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND status = :status";
            $params['status'] = (int)$status;
        }

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR description LIKE :search OR requirements LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = (int)$limit;
            $params['offset'] = (int)$offset;
        }

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            if (in_array($key, ['limit', 'offset', 'status'])) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả tin tuyển dụng active cho User
     */
    public function getActiveJobs($limit = null, $offset = 0, $search = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 1 AND deadline >= CURDATE()";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = (int)$limit;
            $params['offset'] = (int)$offset;
        }

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            if (in_array($key, ['limit', 'offset'])) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tin tuyển dụng theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tin tuyển dụng theo slug
     */
    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo slug từ title
     */
    private function createSlug($title)
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }

    /**
     * Tạo slug duy nhất
     */
    private function generateUniqueSlug($title, $id = null)
    {
        $slug = $this->createSlug($title);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $id)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Kiểm tra slug đã tồn tại chưa
     */
    private function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE slug = :slug";
        $params = ['slug' => $slug];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params['id'] = $excludeId;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Tạo tin tuyển dụng mới
     */
    public function create($data)
    {
        // Tạo slug từ title
        $data['slug'] = $this->generateUniqueSlug($data['title']);

        $sql = "INSERT INTO {$this->table} 
                (title, slug, image, work_location, degree,work_type, quantity, salary_range, 
                 deadline, description, requirements, benefits, status, created_at, updated_at) 
                VALUES 
                (:title, :slug, :image, :work_location, :degree,:work_type, :quantity, :salary_range, 
                 :deadline, :description, :requirements, :benefits, :status, NOW(), NOW())";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'image' => $data['image'] ?? 'default-job.webp',
            'work_location' => $data['work_location'] ?? null,
            'degree' => $data['degree'] ?? 'Cao Đẳng - Đại Học',
            'work_type' => $data['work_type'] ?? 'Toàn thời gian',
            'quantity' => (int)($data['quantity'] ?? 1),
            'salary_range' => $data['salary_range'] ?? null,
            'deadline' => $data['deadline'],
            'description' => $data['description'] ?? null,
            'requirements' => $data['requirements'] ?? null,
            'benefits' => $data['benefits'] ?? null,
            'status' => (int)($data['status'] ?? 1)
        ]);

        return $this->conn->lastInsertId();
    }

    /**
     * Cập nhật tin tuyển dụng
     */
    public function update($id, $data)
    {
        // Cập nhật slug nếu title thay đổi
        if (isset($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title'], $id);
        }

        $fields = [];
        $params = [];

        $allowedFields = [
            'title',
            'slug',
            'image',
            'work_location',
            'degree',
            'work_type',
            'quantity',
            'salary_range',
            'deadline',
            'description',
            'requirements',
            'benefits',
            'status'
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $fields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        if (empty($fields)) {
            return false;
        }

        $params['id'] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Xóa tin tuyển dụng
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id, 'status' => (int)$status]);
    }

    /**
     * Đếm tổng số tin (Admin)
     */
    public function countAdmin($status = null, $search = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND status = :status";
            $params['status'] = (int)$status;
        }

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR description LIKE :search OR requirements LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            if ($key == 'status') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Đếm số tin active cho User
     */
    public function countActive($search = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE status = 1 AND deadline >= CURDATE()";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Tăng lượt xem
     */
    public function incrementViews($id)
    {
        // Nếu có cột views thì thêm, hiện tại chưa có trong SQL
        // Có thể bỏ qua hoặc thêm cột views sau
        return true;
    }
    /**
     * Lấy tin tuyển dụng sắp hết hạn (trong vòng 7 ngày)
     */
    public function getExpiringSoon($limit = 5)
    {
        $sql = "SELECT * FROM {$this->table} 
            WHERE status = 1 
            AND deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            ORDER BY deadline ASC 
            LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Lấy tin tuyển dụng đã quá hạn nộp
     */
    public function getExpiredJobs($limit = null)
    {
        $sql = "SELECT * FROM {$this->table} 
            WHERE status = 1 AND deadline < CURDATE()
            ORDER BY deadline ASC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        } else {
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
