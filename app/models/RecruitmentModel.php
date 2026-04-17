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
     * Lấy tất cả tin tuyển dụng với phân trang và filter
     */
    public function getAll($status = null, $limit = null, $offset = 0, $search = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];
        // Filter theo status (status là số: 1, 0, 2)
        if ($status !== null && $status != '') {
            $sql .= " AND status = :status";
            $params['status'] = (int)$status;
        }
        // Search theo title hoặc description
        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
            $params['search'] = "%{$search}%";
        }
        $sql .= " ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }

        $stmt = $this->conn->prepare($sql);

        foreach ($params as $key => $value) {
            if ($key == 'limit' || $key == 'offset' || $key == 'status') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
     * Tạo tin tuyển dụng mới
     */
    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (recruitment_title, slug, job_description, job_requirements, 
                job_benefits, image, salary_range, location, deadline, quantity, position, 
                experience, education, job_type, status, views, created_at, updated_at) 
                VALUES (:recruitment_title, :slug, :job_description, :job_requirements, 
                :job_benefits, :image, :salary_range, :location, :deadline, :quantity, :position, 
                :experience, :education, :job_type, :status, :views, NOW(), NOW())";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);

        return $this->conn->lastInsertId();
    }

    /**
     * Cập nhật tin tuyển dụng
     */
    public function update($id, $data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "{$key} = :{$key}";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = :id";
        $data['id'] = $id;

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Tăng lượt xem
     */
    public function incrementViews($id)
    {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Lấy tin tuyển dụng đang mở
     */
    public function getOpenJobs($limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'open' AND deadline >= CURDATE() 
                ORDER BY created_at DESC 
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số tin với fitter
     */
    public function count($status = null, $search = null)
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE 1=1";
        $params = [];

        if ($status) {
            $sql .= " AND status = :status";
            $params['status'] = (int)$status;
        }
        if (!empty($search)) {
            $sql .= " AND (title LIKE :search OR description LIKE :search)";
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
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'];
    }

    /**
     * Tìm kiếm tin tuyển dụng
     */
    public function search($keyword, $location = null, $jobType = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (recruitment_title LIKE :keyword OR job_description LIKE :keyword) 
                AND status = 'open'";
        $params = ['keyword' => "%{$keyword}%"];

        if ($location) {
            $sql .= " AND location LIKE :location";
            $params['location'] = "%{$location}%";
        }

        if ($jobType) {
            $sql .= " AND job_type = :job_type";
            $params['job_type'] = $jobType;
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}