<?php
// models/ApplicationModel.php
require_once __DIR__ . '/../core/Model.php';

class ApplicationModel extends Model
{
    protected $table = 'applications';

    // ... giữ nguyên các phương thức hiện có ...

    /**
     * Lấy chi tiết đơn ứng tuyển kèm thông tin bài tuyển dụng
     */
    public function getDetailWithJob($applicationId)
    {
        $sql = "SELECT a.*, r.recruitment_title, r.slug, r.location 
                FROM applications a 
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