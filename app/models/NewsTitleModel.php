<?php
// models/NewsTitleModel.php

require_once __DIR__ . '/../core/Model.php';

class NewsTitleModel extends Model
{
    protected $table = 'news_title';

    public function __construct()
    {
        parent::__construct();
    }

    public function getNewsDetail($slug)
    {
        $sql = "SELECT nt.*, n.author, n.publish_date, n.image as main_image, n.category_id,
                       c.name as category_name, c.slug as category_slug,
                       a.name as author_name, a.bio as author_bio, a.avatar as author_avatar
                FROM news_title nt 
                INNER JOIN news n ON nt.news_id = n.id 
                LEFT JOIN categories c ON n.category_id = c.id 
                LEFT JOIN authors a ON n.author_id = a.id
                WHERE nt.slug = :slug 
                AND nt.status = 'published'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch();
    }

    public function getRelatedNews($newsId, $categoryId, $limit = 5)
    {
        $sql = "SELECT nt.*, n.author, n.publish_date, n.image 
                FROM news_title nt 
                INNER JOIN news n ON nt.news_id = n.id 
                WHERE n.category_id = :category_id 
                AND n.id != :news_id 
                AND nt.status = 'published' 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':news_id', $newsId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBreakingNews($limit = 3)
    {
        $sql = "SELECT nt.*, n.author, n.publish_date, n.image 
                FROM news_title nt 
                INNER JOIN news n ON nt.news_id = n.id 
                WHERE nt.is_breaking = 1 
                AND nt.status = 'published' 
                ORDER BY nt.published_at DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getLatestNews($limit = 10)
    {
        $sql = "SELECT nt.*, n.author, n.publish_date, n.image 
                FROM news_title nt 
                INNER JOIN news n ON nt.news_id = n.id 
                WHERE nt.status = 'published' 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function incrementViews($id)
    {
        $sql = "UPDATE news_title SET views = views + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function incrementShareCount($id)
    {
        $sql = "UPDATE news_title SET share_count = share_count + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}

?>