<?php
// models/RecruitmentTitleModel.php

require_once __DIR__ . '/../core/Model.php';

class RecruitmentTitleModel extends Model
{
    protected $table = 'recruitment_title';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lấy danh sách tuyển dụng đang hiển thị
     */
    public function getActiveRecruitments($limit = null)
    {
        $sql = "SELECT * FROM recruitment_title 
                WHERE status = 1 
                AND deadline >= CURDATE() 
                ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare($sql);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết tuyển dụng
     */
    public function getDetail($slug)
    {
        $sql = "SELECT * FROM recruitment_title WHERE slug = :slug AND status = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    /**
     * Lấy tuyển dụng nổi bật
     */
    public function getFeaturedRecruitments($limit = 5)
    {
        $sql = "SELECT * FROM recruitment_title 
                WHERE status = 1 
                AND featured = 1 
                AND deadline >= CURDATE() 
                ORDER BY created_at DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tìm kiếm tuyển dụng
     */
    public function search($keyword)
    {
        $sql = "SELECT * FROM recruitment_title 
                WHERE status = 1 
                AND (title LIKE :keyword 
                     OR position LIKE :keyword 
                     OR work_location LIKE :keyword)
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['keyword' => "%{$keyword}%"]);
        return $stmt->fetchAll();
    }

    /**
     * Tăng lượt xem
     */
    public function incrementViews($id)
    {
        $sql = "UPDATE recruitment_title SET views = views + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}