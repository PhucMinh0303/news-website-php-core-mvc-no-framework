<?php
// app/models/Recruitment.php
require_once __DIR__ . '/../core/Model.php';

class Recruitment extends Model
{
    protected $table = 'recruitments';
    protected $primaryKey = 'id';

    /**
     * Get all open recruitments
     */
    public function getAllOpen()
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'open' 
                AND deadline >= CURDATE() 
                ORDER BY created_at DESC";
        return $this->query($sql);
    }

    /**
     * Get recruitment by slug
     */
    public function getBySlug($slug)
    {
        $sql = "SELECT * FROM {$this->table} WHERE slug = ?";
        $result = $this->query($sql, [$slug]);
        return $result ? $result[0] : null;
    }

    /**
     * Get recruitment by ID
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->query($sql, [$id]);
        return $result ? $result[0] : null;
    }

    /**
     * Increment view count
     */
    public function incrementViews($id)
    {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = ?";
        return $this->execute($sql, [$id]);
    }

    /**
     * Get recruitments by status
     */
    public function getByStatus($status)
    {
        $sql = "SELECT * FROM {$this->table} WHERE status = ? ORDER BY created_at DESC";
        return $this->query($sql, [$status]);
    }

    /**
     * Search recruitments
     */
    public function search($keyword)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (recruitment_title LIKE ? OR job_description LIKE ?) 
                AND status = 'open' 
                ORDER BY created_at DESC";
        $searchTerm = "%{$keyword}%";
        return $this->query($sql, [$searchTerm, $searchTerm]);
    }
}

?>