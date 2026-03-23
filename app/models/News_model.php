<?php
// models/NewsModel.php
require_once __DIR__ . '/../core/Model.php';

class NewsModel extends Model
{
    protected $table = 'news';

    public function getPublishedNews($limit = 10)
    {
        $sql = "SELECT n.*, c.name as category_name, c.slug as category_slug 
                FROM news n
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE n.status = 'published'
                ORDER BY n.publish_date DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$limit]);
    }

    public function getNewsByCategory($categoryId, $limit = 10)
    {
        $sql = "SELECT n.*, c.name as category_name 
                FROM news n
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE n.category_id = ? AND n.status = 'published'
                ORDER BY n.publish_date DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$categoryId, $limit]);
    }

    public function getNewsBySlug($slug)
    {
        $sql = "SELECT n.*, c.name as category_name, a.full_name as author_name
                FROM news n
                LEFT JOIN categories c ON n.category_id = c.id
                LEFT JOIN authors a ON n.author_id = a.id
                WHERE n.slug = ? AND n.status = 'published'";

        $news = $this->db->fetchOne($sql, [$slug]);

        if ($news) {
            // Increment view count
            $this->incrementViews($news['id']);
        }

        return $news;
    }

    public function incrementViews($id)
    {
        $sql = "UPDATE news SET views = views + 1 WHERE id = ?";
        $this->db->query($sql, [$id]);
    }

    public function searchNews($keyword, $limit = 20)
    {
        $sql = "SELECT n.*, c.name as category_name
                FROM news n
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE (n.title LIKE ? OR n.content LIKE ?)
                AND n.status = 'published'
                ORDER BY n.publish_date DESC
                LIMIT ?";

        $searchTerm = "%{$keyword}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $limit]);
    }

    public function getRelatedNews($newsId, $categoryId, $limit = 5)
    {
        $sql = "SELECT n.*, c.name as category_name
                FROM news n
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE n.category_id = ? 
                AND n.id != ?
                AND n.status = 'published'
                ORDER BY n.publish_date DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$categoryId, $newsId, $limit]);
    }

    public function getNewsByDateRange($startDate, $endDate)
    {
        $sql = "SELECT n.*, c.name as category_name
                FROM news n
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE n.publish_date BETWEEN ? AND ?
                AND n.status = 'published'
                ORDER BY n.publish_date DESC";

        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }

    public function getNewsWithLinks($id)
    {
        $sql = "SELECT * FROM vw_news_links WHERE news_id = ?";
        return $this->db->fetchAll($sql, [$id]);
    }
}