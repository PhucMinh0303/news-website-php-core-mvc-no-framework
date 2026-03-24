<?php
// models/NewsTitleModel.php
require_once __DIR__ . '/../core/Model.php';

class NewsTitleModel extends Model
{
    protected $table = 'news_title';

    public function getNewsDetailBySlug($slug)
    {
        $sql = "SELECT nt.*, n.author, n.category_id, n.author_id, n.publish_date,
                       c.name as category_name, c.slug as category_slug,
                       a.full_name as author_name, a.avatar as author_avatar,
                       a.bio as author_bio
                FROM news_title nt
                INNER JOIN news n ON nt.news_id = n.id
                LEFT JOIN categories c ON n.category_id = c.id
                LEFT JOIN authors a ON n.author_id = a.id
                WHERE nt.slug = ? AND nt.status = 'published'
                AND (nt.published_at <= NOW() OR nt.published_at IS NULL)";

        $news = $this->db->fetchOne($sql, [$slug]);

        if ($news) {
            $this->incrementViews($news['news_id']);
        }

        return $news;
    }

    public function incrementViews($newsId)
    {
        // Update views in news_title table
        $sql = "UPDATE news_title SET views = views + 1 WHERE news_id = ?";
        $this->db->query($sql, [$newsId]);

        // Update views in news table
        $sql = "UPDATE news SET views = views + 1 WHERE id = ?";
        $this->db->query($sql, [$newsId]);

        // Log view
        $this->logView($newsId);
    }

    public function logView($newsId)
    {
        $sql = "INSERT INTO news_views_stats (news_id, ip_address, user_agent, referrer) 
                VALUES (?, ?, ?, ?)";

        $this->db->query($sql, [
            $newsId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $_SERVER['HTTP_REFERER'] ?? null
        ]);
    }

    public function getTagsByNewsId($newsId)
    {
        $sql = "SELECT t.* 
                FROM news_tags t
                INNER JOIN news_tag_relations tr ON t.id = tr.tag_id
                WHERE tr.news_id = ?
                ORDER BY t.name";

        return $this->db->fetchAll($sql, [$newsId]);
    }

    public function getCommentsByNewsId($newsId, $approvedOnly = true, $limit = 50)
    {
        $sql = "SELECT * FROM news_comments 
                WHERE news_id = ?";

        if ($approvedOnly) {
            $sql .= " AND is_approved = 1";
        }

        $sql .= " ORDER BY created_at DESC LIMIT ?";

        return $this->db->fetchAll($sql, [$newsId, $limit]);
    }

    public function addComment($newsId, $data)
    {
        $sql = "INSERT INTO news_comments (news_id, parent_id, author_name, author_email, 
                author_website, author_ip, content, is_approved) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $isApproved = $data['auto_approve'] ?? false ? 1 : 0;

        $this->db->query($sql, [
            $newsId,
            $data['parent_id'] ?? null,
            $data['author_name'],
            $data['author_email'] ?? null,
            $data['author_website'] ?? null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $data['content'],
            $isApproved
        ]);

        return $this->db->getConnection()->lastInsertId();
    }

    public function getRelatedNews($newsId, $limit = 5)
    {
        $sql = "SELECT n.*, nt.title, nt.slug, nt.featured_image, nt.description,
                       c.name as category_name
                FROM news_related nr
                INNER JOIN news n ON nr.related_news_id = n.id
                INNER JOIN news_title nt ON n.id = nt.news_id
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE nr.news_id = ? AND nt.status = 'published'
                ORDER BY nr.sort_order, nr.created_at DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$newsId, $limit]);
    }

    public function getPopularNews($limit = 5, $days = 7)
    {
        $sql = "SELECT nt.*, n.publish_date, c.name as category_name,
                       (nt.views + nt.share_count * 2 + nt.like_count * 3) as popularity_score
                FROM news_title nt
                INNER JOIN news n ON nt.news_id = n.id
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE nt.status = 'published'
                AND nt.published_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY popularity_score DESC
                LIMIT ?";

        return $this->db->fetchAll($sql, [$days, $limit]);
    }

    public function updateShareCount($newsId)
    {
        $sql = "UPDATE news_title SET share_count = share_count + 1 WHERE news_id = ?";
        return $this->db->query($sql, [$newsId]);
    }

    public function updateLikeCount($newsId, $increment = true)
    {
        $sql = "UPDATE news_title SET like_count = like_count + " . ($increment ? 1 : -1) .
            " WHERE news_id = ? AND like_count >= 0";
        return $this->db->query($sql, [$newsId]);
    }

    public function getPreviousNews($newsId, $publishDate)
    {
        $sql = "SELECT nt.*, n.slug as news_slug
                FROM news_title nt
                INNER JOIN news n ON nt.news_id = n.id
                WHERE n.publish_date < ? AND nt.status = 'published'
                ORDER BY n.publish_date DESC
                LIMIT 1";

        return $this->db->fetchOne($sql, [$publishDate]);
    }

    public function getNextNews($newsId, $publishDate)
    {
        $sql = "SELECT nt.*, n.slug as news_slug
                FROM news_title nt
                INNER JOIN news n ON nt.news_id = n.id
                WHERE n.publish_date > ? AND nt.status = 'published'
                ORDER BY n.publish_date ASC
                LIMIT 1";

        return $this->db->fetchOne($sql, [$publishDate]);
    }
}