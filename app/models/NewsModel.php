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

    /**
     * Lấy danh sách bài viết đã publish cho frontend
     */
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tổng số bài viết đã publish
     */
    public function getTotalPublished()
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM news WHERE status = 'published'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Lấy bài viết nổi bật (hot)
     */
    public function getHotNews($limit = 5)
    {
        $sql = "SELECT n.*, c.name as category_name
                FROM news n 
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE n.status = 'published' 
                ORDER BY n.views DESC, n.publish_date DESC 
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy bài viết mới nhất
     */
    public function getLatestNews($limit = 5)
    {
        $sql = "SELECT n.*, c.name as category_name
                FROM news n 
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE n.status = 'published' 
                ORDER BY n.publish_date DESC 
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy bài viết theo slug
     */
    public function getBySlug($slug)
    {
        $sql = "SELECT n.*, c.name as category_name 
                FROM news n 
                LEFT JOIN categories c ON n.category_id = c.id 
                WHERE n.slug = :slug";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy bài viết theo ID
     */
    public function getById($id)
    {
        $sql = "SELECT n.*, c.name as category_name 
                FROM news n 
                LEFT JOIN categories c ON n.category_id = c.id 
                WHERE n.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách bài viết cho admin (có filter)
     */
    public function getAllAdmin($status = null, $limit = 10, $offset = 0, $search = null)
    {
        $sql = "SELECT n.*, c.name as category_name 
                FROM news n 
                LEFT JOIN categories c ON n.category_id = c.id 
                WHERE 1=1";
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND n.status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $sql .= " AND (n.title LIKE :search OR n.author LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $sql .= " ORDER BY n.publish_date DESC, n.created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm tổng số bài viết cho admin
     */
    public function countAdmin($status = null, $search = null)
    {
        $sql = "SELECT COUNT(*) as total FROM news WHERE 1=1";
        $params = [];

        if ($status !== null && $status !== '') {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }

        if ($search) {
            $sql .= " AND (title LIKE :search OR author LIKE :search)";
            $params[':search'] = "%{$search}%";
        }

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Lấy thống kê số lượng bài viết theo trạng thái
     */
    public function getStats()
    {
        $sql = "SELECT 
                    SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                    SUM(CASE WHEN status = 'archived' THEN 1 ELSE 0 END) as archived,
                    COUNT(*) as total
                FROM news";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tạo slug từ tiêu đề
     */
    public function createSlug($string)
    {
        $string = trim($string);
        $string = strtolower($string);
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }

    /**
     * Kiểm tra slug đã tồn tại chưa
     */
    public function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM news WHERE slug = :slug";
        $params = [':slug' => $slug];
        
        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Tạo slug duy nhất
     */
    public function generateUniqueSlug($title, $excludeId = null)
    {
        $slug = $this->createSlug($title);
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Upload ảnh
     */
    public function uploadImage($file)
    {
        $targetDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/news/';
        
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . '_' . bin2hex(random_bytes(5)) . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $targetFile = $targetDir . $fileName;
        $relativePath = '/uploads/news/' . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return ['success' => true, 'path' => $relativePath];
        }
        
        return ['success' => false, 'error' => 'Upload failed'];
    }

    /**
     * Xóa ảnh
     */
    public function deleteImage($imagePath)
    {
        if ($imagePath && file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)) {
            return unlink($_SERVER['DOCUMENT_ROOT'] . $imagePath);
        }
        return false;
    }

    /**
     * Tính thời gian đọc
     */
    public function calculateReadingTime($content)
    {
        $text = strip_tags($content);
        $wordCount = str_word_count($text, 0, 'áàạảãâấầậẩẫăắằặẳẵéèẹẻẽêếềệểễóòọỏõôốồộổỗơớờợởỡúùụủũưứừựửữýỳỵỷỹđ');
        $wordsPerMinute = 200;
        $readingTime = ceil($wordCount / $wordsPerMinute);
        return max(1, $readingTime);
    }

    /**
     * Tăng lượt xem
     */
    public function incrementViews($id)
    {
        $sql = "UPDATE news SET views = views + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Tạo bài viết mới
     */
    public function create($data)
    {
        try {
            $sql = "INSERT INTO news (title, slug, category_id, author_id, author, publish_date, image, content, views, status) 
                    VALUES (:title, :slug, :category_id, :author_id, :author, :publish_date, :image, :content, :views, :status)";
            
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                ':title' => $data['title'],
                ':slug' => $data['slug'],
                ':category_id' => $data['category_id'] ?? null,
                ':author_id' => $data['author_id'] ?? null,
                ':author' => $data['author'],
                ':publish_date' => $data['publish_date'] ?? date('Y-m-d'),
                ':image' => $data['image'] ?? null,
                ':content' => $data['content'],
                ':views' => $data['views'] ?? 0,
                ':status' => $data['status'] ?? 'draft'
            ]);
            
            if ($result) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating news: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật bài viết
     */
    public function update($id, $data)
    {
        try {
            $sql = "UPDATE news SET 
                        title = :title,
                        slug = :slug,
                        category_id = :category_id,
                        author_id = :author_id,
                        author = :author,
                        publish_date = :publish_date,
                        image = :image,
                        content = :content,
                        status = :status
                    WHERE id = :id";
            
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':title' => $data['title'],
                ':slug' => $data['slug'],
                ':category_id' => $data['category_id'] ?? null,
                ':author_id' => $data['author_id'] ?? null,
                ':author' => $data['author'],
                ':publish_date' => $data['publish_date'] ?? date('Y-m-d'),
                ':image' => $data['image'] ?? null,
                ':content' => $data['content'],
                ':status' => $data['status'] ?? 'draft'
            ]);
        } catch (PDOException $e) {
            error_log("Error updating news: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id, $status)
    {
        $sql = "UPDATE news SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id, ':status' => $status]);
    }

    /**
     * Xóa bài viết
     */
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM news WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error deleting news: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách categories
     */
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

    /**
     * Lấy danh sách authors
     */
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
}
?>