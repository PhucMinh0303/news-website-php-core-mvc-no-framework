<?php
// models/NewsModel.php

require_once '../core/Model.php';

class NewsModel extends Model
{
    protected $table = 'news';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Lấy tất cả bài viết đã publish
     */
    public function getPublishedNews($limit = null, $offset = 0): array
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

    /**
     * Lấy bài viết hot
     */
    public function getHotNews($limit = 5)
    {
        $sql = "SELECT n.*, nt.is_hot 
                FROM news n 
                INNER JOIN news_title nt ON n.id = nt.news_id 
                WHERE n.status = 'published' 
                AND nt.is_hot = TRUE 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Lấy bài viết nổi bật
     */
    public function getFeaturedNews($limit = 5)
    {
        $sql = "SELECT n.*, nt.is_featured 
                FROM news n 
                INNER JOIN news_title nt ON n.id = nt.news_id 
                WHERE n.status = 'published' 
                AND nt.is_featured = TRUE 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tăng lượt xem
     */
    public function incrementViews($id)
    {
        $sql = "UPDATE news SET views = views + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Lấy bài viết theo category
     */
    public function getByCategory($categoryId, $limit = null)
    {
        $sql = "SELECT n.*, c.name as category_name 
                FROM news n 
                LEFT JOIN categories c ON n.category_id = c.id 
                WHERE n.status = 'published' 
                AND n.category_id = :category_id 
                ORDER BY n.publish_date DESC";

        if ($limit) {
            $sql .= " LIMIT :limit";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare($sql);
        }

        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Tìm kiếm bài viết
     */
    public function search($keyword)
    {
        $sql = "SELECT n.*, nt.title, nt.description, nt.content 
                FROM news n 
                INNER JOIN news_title nt ON n.id = nt.news_id 
                WHERE n.status = 'published' 
                AND (n.title LIKE :keyword 
                     OR nt.title LIKE :keyword 
                     OR nt.description LIKE :keyword 
                     OR nt.content LIKE :keyword)
                ORDER BY n.publish_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['keyword' => "%{$keyword}%"]);
        return $stmt->fetchAll();
    }
}