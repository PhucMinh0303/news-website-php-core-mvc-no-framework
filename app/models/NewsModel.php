<?php
// app/models/NewsModel.php

require_once __DIR__ . '/../core/Model.php';

class NewsModel extends Model
{
    protected $table = 'news';

    public function __construct()
    {
        parent::__construct();
    }

    public function getPublishedNews($limit = null, $offset = 0)
    {
        $sql = "SELECT n.*, c.name as category_name 
                FROM news n 
                LEFT JOIN categories c ON n.category_id = c.id 
                WHERE n.status = 'published' 
                ORDER BY n.publish_date DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare($sql);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalPublished()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM news WHERE status = 'published'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getHotNews($limit = 5)
    {
        $sql = "SELECT n.*, nt.is_hot, nt.title as full_title
                FROM news n 
                INNER JOIN news_title nt ON n.id = nt.news_id 
                WHERE n.status = 'published' 
                AND nt.is_hot = 1 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFeaturedNews($limit = 5)
    {
        $sql = "SELECT n.*, nt.is_featured, nt.title as full_title
                FROM news n 
                INNER JOIN news_title nt ON n.id = nt.news_id 
                WHERE n.status = 'published' 
                AND nt.is_featured = 1 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function incrementViews($id)
    {
        $sql = "UPDATE news SET views = views + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}

?>