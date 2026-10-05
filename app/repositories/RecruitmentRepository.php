<?php
// app/repositories/RecruitmentRepository.php

require_once __DIR__ . '/../core/Database.php';

class RecruitmentRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Lấy danh sách tin tuyển dụng active với phân trang
     */
    public function getActiveRecruitmentsPaginated($limit, $offset)
    {
        try {
            $sql = "SELECT * FROM recruitments 
                    WHERE status = 1 AND deadline >= CURDATE()
                    ORDER BY created_at DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get active recruitments paginated error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chi tiết tin tuyển dụng theo slug
     */
    public function getDetail($slug)
    {
        try {
            $sql = "SELECT * FROM recruitments WHERE slug = :slug AND status = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['slug' => $slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get detail error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy tin tuyển dụng theo ID, bao gồm cả bản nháp và tin đã đóng.
     */
    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM recruitments WHERE id = :id');
            $stmt->execute(['id' => (int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Get recruitment by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get a recruitment by slug, including drafts and closed records.
     */
    public function getBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM recruitments WHERE slug = :slug');
            $stmt->execute(['slug' => $slug]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            error_log("Get recruitment by slug error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update recruitment data by ID.
     */
    public function update($id, $data)
    {
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
        $fields = [];
        $params = ['id' => (int)$id];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = :{$field}";
                $params[$field] = $data[$field];
            }
        }

        if (empty($fields)) {
            return false;
        }

        try {
            $sql = 'UPDATE recruitments SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = :id';
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update recruitment error: " . $e->getMessage());
            return false;
        }
    }

    public function slugExists($slug, $excludeId = null)
    {
        $sql = 'SELECT COUNT(*) FROM recruitments WHERE slug = :slug';
        $params = ['slug' => $slug];
        if ($excludeId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = (int)$excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Xóa tin tuyển dụng theo ID.
     */
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM recruitments WHERE id = ?');
            $stmt->execute([(int)$id]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Delete recruitment error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tăng lượt xem
     */
    public function incrementViews($id)
    {
        try {
            $sql = "UPDATE recruitments SET views = views + 1 WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Increment views error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đếm số tin active
     */
    public function countActive()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM recruitments WHERE status = 1 AND deadline >= CURDATE()";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Count active error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy tin tuyển dụng theo vị trí
     */
    public function getByPosition($position, $limit = 5)
    {
        try {
            $sql = "SELECT * FROM recruitments 
                    WHERE status = 1 AND deadline >= CURDATE() AND work_type = :position
                    ORDER BY created_at DESC
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':position', $position, PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get by position error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tin tuyển dụng nổi bật
     */
    public function getFeaturedRecruitments($limit = 5)
    {
        // Giả sử có cột featured trong bảng, nếu không có thì lấy tin mới nhất
        try {
            $sql = "SELECT * FROM recruitments 
                    WHERE status = 1 AND deadline >= CURDATE()
                    ORDER BY created_at DESC
                    LIMIT :limit";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get featured recruitments error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách vị trí công việc
     */
    public function getAllPositions()
    {
        try {
            $sql = "SELECT DISTINCT work_type FROM recruitments WHERE status = 1 ORDER BY work_type";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Get all positions error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tìm kiếm tin tuyển dụng
     */
    public function search($keyword, $limit = 20, $offset = 0)
    {
        try {
            $sql = "SELECT * FROM recruitments 
                    WHERE status = 1 AND deadline >= CURDATE()
                    AND (title LIKE :keyword OR description LIKE :keyword OR requirements LIKE :keyword)
                    ORDER BY created_at DESC
                    LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Search error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy tin tuyển dụng active
     */
    public function getActiveRecruitments($limit = 10)
    {
        return $this->getActiveRecruitmentsPaginated($limit, 0);
    }
}
