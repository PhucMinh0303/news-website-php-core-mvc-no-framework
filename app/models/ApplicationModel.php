<?php
// models/ApplicationModel.php
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/RecruitmentModel.php';

class ApplicationModel extends Model
{
    protected $table = 'job_applications';

    /**
     * Lưu hồ sơ ứng tuyển
     */
    public function createApplication($data)
    {
        $sql = "INSERT INTO {$this->table}
                    (recruitment_id, full_name, phone, email, content, cv_file)
                VALUES
                    (:recruitment_id, :full_name, :phone, :email, :content, :cv_file)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'recruitment_id' => $data['recruitment_id'],
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'content' => $data['content'] ?? null,
            'cv_file' => $data['cv_file']
        ]);
    }

    /**
     * Lấy danh sách hồ sơ ứng tuyển cho trang quản trị
     */
    public function getAllApplications()
    {
        $sql = "SELECT a.*, r.title AS recruitment_title
                FROM {$this->table} a
                LEFT JOIN recruitments r ON a.recruitment_id = r.id
                ORDER BY a.created_at DESC, a.id DESC";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy chi tiết đơn ứng tuyển kèm thông tin bài tuyển dụng
     */
    public function getDetailWithJob($applicationId)
    {
        $sql = "SELECT a.*, r.title AS recruitment_title, r.slug, r.work_location
                FROM {$this->table} a
                JOIN recruitments r ON a.recruitment_id = r.id 
                WHERE a.id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $applicationId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Cập nhật trạng thái đơn ứng tuyển
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id, 'status' => $status]);
    }

    /**
     * Thống kê số lượng ứng viên theo bài tuyển dụng
     */
    public function countByRecruitment($recruitmentId = null)
    {
        $sql = "SELECT recruitment_id, COUNT(*) as total FROM {$this->table}";
        $params = [];

        if ($recruitmentId) {
            $sql .= " WHERE recruitment_id = :recruitment_id";
            $params['recruitment_id'] = $recruitmentId;
        }

        $sql .= " GROUP BY recruitment_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}