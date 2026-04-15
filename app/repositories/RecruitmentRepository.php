<?php
/**
 * RecruitmentRepository
 * Implementation of IRecruitmentRepository for recruitment_title table
 */

require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../interfaces/IRecruitmentRepository.php';

class RecruitmentRepository extends Model implements IRecruitmentRepository
{
    protected $table = 'recruitment_title';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active recruitments with pagination
     */
    public function getActiveRecruitmentsPaginated($limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 1 
                AND deadline >= CURDATE() 
                ORDER BY created_at DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all active recruitments
     */
    public function getActiveRecruitments($limit = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 1 
                AND deadline >= CURDATE() 
                ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        } else {
            $stmt = $this->connection->prepare($sql);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recruitment detail by slug
     */
    public function getDetail($slug)
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug AND status = 1";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get featured recruitments
     */
    public function getFeaturedRecruitments($limit = 5)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 1 
                AND featured = 1 
                AND deadline >= CURDATE() 
                ORDER BY created_at DESC 
                LIMIT :limit";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get recruitments by position
     */
    public function getByPosition($position, $limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 1 
                AND position = :position 
                AND deadline >= CURDATE() 
                ORDER BY created_at DESC 
                LIMIT :limit";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':position', $position, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search recruitments
     */
    public function search($keyword, $limit = null, $offset = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 1 
                AND (title LIKE :keyword 
                     OR position LIKE :keyword 
                     OR work_location LIKE :keyword
                     OR description LIKE :keyword)
                ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
            }
        }

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(':keyword', "%{$keyword}%", PDO::PARAM_STR);

        if ($limit !== null) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Count active recruitments
     */
    public function countActive()
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} 
                WHERE status = 1 AND deadline >= CURDATE()";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ?? 0;
    }

    /**
     * Increment view count
     */
    public function incrementViews($id)
    {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = :id";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get all positions
     */
    public function getAllPositions()
    {
        $sql = "SELECT DISTINCT position FROM {$this->table} 
                WHERE status = 1 AND deadline >= CURDATE() 
                ORDER BY position ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

