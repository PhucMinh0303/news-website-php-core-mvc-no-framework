<?php
require_once __DIR__ . '/BaseModel.php';

class RecruitmentModel extends BaseModel
{

    // Lấy tất cả tuyển dụng đang mở
    public function getAllOpenRecruitments($limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE status = 'open' 
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?";

        return $this->select($sql, [$limit, $offset], "ii");
    }

    // Lấy chi tiết tuyển dụng theo slug
    public function getRecruitmentBySlug($slug)
    {
        $sql = "SELECT * FROM recruitments WHERE slug = ?";
        return $this->selectOne($sql, [$slug], "s");
    }

    // Lấy chi tiết tuyển dụng theo slug từ bảng recruitment_title
    public function getRecruitmentTitleBySlug($slug)
    {
        $sql = "SELECT * FROM recruitment_title WHERE slug = ? AND status = 1";
        return $this->selectOne($sql, [$slug], "s");
    }

    // Lấy tuyển dụng nổi bật
    public function getFeaturedRecruitments($limit = 5)
    {
        $sql = "SELECT * FROM recruitment_title 
                WHERE status = 1 AND featured = 1
                ORDER BY created_at DESC
                LIMIT ?";

        return $this->select($sql, [$limit], "i");
    }

    // Tăng lượt xem tuyển dụng
    public function incrementViews($recruitmentId, $table = 'recruitments')
    {
        $sql = "UPDATE {$table} SET views = views + 1 WHERE id = ?";
        return $this->execute($sql, [$recruitmentId], "i");
    }

    // Lọc tuyển dụng theo vị trí
    public function getRecruitmentsByPosition($position, $limit = 10)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE status = 'open' AND position LIKE ?
                ORDER BY created_at DESC
                LIMIT ?";

        $position = "%{$position}%";
        return $this->select($sql, [$position, $limit], "si");
    }

    // Lọc tuyển dụng theo địa điểm
    public function getRecruitmentsByLocation($location, $limit = 10)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE status = 'open' AND location LIKE ?
                ORDER BY created_at DESC
                LIMIT ?";

        $location = "%{$location}%";
        return $this->select($sql, [$location, $limit], "si");
    }

    // Lấy danh sách vị trí tuyển dụng (để lọc)
    public function getDistinctPositions()
    {
        $sql = "SELECT DISTINCT position FROM recruitments WHERE status = 'open' ORDER BY position";
        return $this->select($sql);
    }
}

?>