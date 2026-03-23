<?php
// models/RecruitmentModel.php
require_once __DIR__ . '/../core/Model.php';

class RecruitmentModel extends Model
{
    protected $table = 'recruitments';

    public function getOpenRecruitments($limit = 10)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE status = 'open' 
                AND (deadline IS NULL OR deadline >= CURDATE())
                ORDER BY created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    public function getRecruitmentBySlug($slug)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE slug = ? 
                AND status = 'open'";

        $recruitment = $this->db->fetchOne($sql, [$slug]);

        if ($recruitment) {
            $this->incrementViews($recruitment['id']);
        }

        return $recruitment;
    }

    public function incrementViews($id)
    {
        $sql = "UPDATE recruitments SET views = views + 1 WHERE id = ?";
        $this->db->query($sql, [$id]);
    }

    public function searchRecruitments($keyword, $location = null)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE status = 'open'
                AND (recruitment_title LIKE ? OR job_description LIKE ?)";
        $params = ["%{$keyword}%", "%{$keyword}%"];

        if ($location) {
            $sql .= " AND location LIKE ?";
            $params[] = "%{$location}%";
        }

        $sql .= " ORDER BY created_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    public function getRecruitmentsByType($jobType, $limit = 10)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE job_type = ? 
                AND status = 'open'
                AND (deadline IS NULL OR deadline >= CURDATE())
                ORDER BY created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$jobType, $limit]);
    }
}