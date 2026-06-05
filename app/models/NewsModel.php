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
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        } else {
            $stmt = $this->conn->prepare($sql);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTotalPublished()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM news WHERE status = 'published'");
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

        $stmt = $this->conn->prepare($sql);
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

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createNews($data)
    {
        try {
            $sql = "INSERT INTO news (category_id, author, views, status, created_at, updated_at) 
                    VALUES (:category_id, :author, :views, :status, NOW(), NOW())";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':category_id' => $data['category_id'],
                ':author' => $data['author_name'],
                ':views' => $data['views'] ?? 0,
                ':status' => $data['status']
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating news: " . $e->getMessage());
            return false;
        }
    }

    public function getCategories()
    {
        try {
            $stmt = $this->conn->query("SELECT id, name, slug FROM categories WHERE status = 'active' ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting categories: " . $e->getMessage());
            return [];
        }
    }

    public function getAuthors()
    {
        try {
            $stmt = $this->conn->query("SELECT id, name, email, bio FROM authors WHERE status = 'active' ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting authors: " . $e->getMessage());
            return [];
        }
    }

    public function calculateReadingTime($content)
    {
        $text = strip_tags($content);
        $wordCount = str_word_count($text, 0, 'áàạảãâấầậẩẫăắằặẳẵéèẹẻẽêếềệểễóòọỏõôốồộổỗơớờợởỡúùụủũưứừựửữýỳỵỷỹđ');
        $wordsPerMinute = 200;
        $readingTime = ceil($wordCount / $wordsPerMinute);
        return max(1, $readingTime);
    }

    public function incrementViews($id)
    {
        $sql = "UPDATE news SET views = views + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
