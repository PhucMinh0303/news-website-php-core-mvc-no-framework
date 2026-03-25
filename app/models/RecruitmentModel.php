<?php
// app/models/Recruitment.php

class Recruitment_model
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /**
     * Lấy tất cả tuyển dụng (chỉ lấy những job đang mở)
     */
    public function getAll()
    {
        $sql = "SELECT * FROM recruitments 
                WHERE status = 'open' 
                ORDER BY created_at DESC";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy tuyển dụng theo slug
     */
    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM recruitments WHERE slug = ? AND status = 'open'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();

        // Tăng views
        if ($row = $result->fetch_assoc()) {
            $this->increaseViews($row['id']);
            return $row;
        }
        return null;
    }

    /**
     * Lấy tuyển dụng theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM recruitments WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Lấy tuyển dụng liên quan (khác với job hiện tại)
     */
    public function getRelated($currentId, $limit = 4)
    {
        $sql = "SELECT * FROM recruitments 
                WHERE id != ? AND status = 'open' 
                ORDER BY created_at DESC 
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $currentId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Tăng lượt xem
     */
    private function increaseViews($id)
    {
        $sql = "UPDATE recruitments SET views = views + 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }

    /**
     * Lọc tuyển dụng theo điều kiện
     */
    public function filter($conditions = [])
    {
        $sql = "SELECT * FROM recruitments WHERE status = 'open'";
        $params = [];
        $types = "";

        if (!empty($conditions['location'])) {
            $sql .= " AND location LIKE ?";
            $params[] = "%" . $conditions['location'] . "%";
            $types .= "s";
        }

        if (!empty($conditions['position'])) {
            $sql .= " AND position LIKE ?";
            $params[] = "%" . $conditions['position'] . "%";
            $types .= "s";
        }

        if (!empty($conditions['job_type'])) {
            $sql .= " AND job_type = ?";
            $params[] = $conditions['job_type'];
            $types .= "s";
        }

        $sql .= " ORDER BY created_at DESC";

        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

?>