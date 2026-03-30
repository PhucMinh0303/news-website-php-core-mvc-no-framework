<?php
// models/RecruitmentModel.php

require_once '../core/Model.php';

class RecruitmentModel extends Model
{
    protected $table = 'recruitments';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lấy danh sách tin tuyển dụng đang mở
     */
    public function getOpenRecruitments($limit = null)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE status = 'open' 
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
     * Lấy chi tiết tin tuyển dụng
     */
    public function getRecruitmentDetail($slug)
    {
        $sql = "SELECT * FROM recruitments WHERE slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    /**
     * Tăng lượt xem
     */
    public function incrementViews($id)
    {
        $sql = "UPDATE recruitments SET views = views + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Tìm kiếm tuyển dụng
     */
    public function search($keyword)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE status = 'open' 
                AND (recruitment_title LIKE :keyword 
                     OR position LIKE :keyword 
                     OR location LIKE :keyword)
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['keyword' => "%{$keyword}%"]);
        return $stmt->fetchAll();
    }

    /**
     * Lọc theo vị trí
     */
    public function filterByPosition($position)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE status = 'open' 
                AND position = :position 
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['position' => $position]);
        return $stmt->fetchAll();
    }
}