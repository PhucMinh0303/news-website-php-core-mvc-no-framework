<?php

require_once __DIR__ . '/../core/Model.php';

class NewsTitleModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Tạo slug từ tiêu đề
     */
    public function createSlug($title)
    {
        // Loại bỏ dấu tiếng Việt
        $slug = $this->removeAccent($title);
        
        // Chỉ giữ chữ cái, số và dấu cách
        $slug = preg_replace('/[^a-zA-Z0-9\s]/', '', $slug);
        $slug = preg_replace('/\s+/', '-', trim($slug));
        $slug = strtolower($slug);
        
        // Xử lý slug rỗng
        if (empty($slug)) {
            $slug = 'bai-viet';
        }

        // Kiểm tra slug đã tồn tại chưa
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
    
    /**
     * Xóa dấu tiếng Việt
     */
    private function removeAccent($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'd' => 'đ',
            'D' => 'Đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
        );
        
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        
        return $str;
    }

    /**
     * Lấy tất cả bài viết (cho admin)
     */
    public function getAllAdmin($status = null, $limit = 10, $offset = 0, $search = null)
    {
        try {
            $sql = "SELECT nt.*, n.category_id, n.author, n.status as news_status
                    FROM news_title nt 
                    INNER JOIN news n ON n.id = nt.news_id
                    WHERE 1=1";
            $params = [];
            
            if ($status && $status !== 'all') {
                $sql .= " AND nt.status = :status";
                $params[':status'] = $status;
            }
            
            if ($search) {
                $sql .= " AND (nt.title LIKE :search OR nt.description LIKE :search)";
                $params[':search'] = "%{$search}%";
            }
            
            $sql .= " ORDER BY nt.created_at DESC LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all news: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm số lượng bài viết (cho admin)
     */
    public function countAdmin($status = null, $search = null)
    {
        try {
            $sql = "SELECT COUNT(*) FROM news_title nt 
                    INNER JOIN news n ON n.id = nt.news_id
                    WHERE 1=1";
            $params = [];
            
            if ($status && $status !== 'all') {
                $sql .= " AND nt.status = :status";
                $params[':status'] = $status;
            }
            
            if ($search) {
                $sql .= " AND (nt.title LIKE :search OR nt.description LIKE :search)";
                $params[':search'] = "%{$search}%";
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting news: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Lấy bài viết theo ID
     */
    public function getById($id)
    {
        try {
            $sql = "SELECT nt.*, n.category_id, n.author as author_name, n.status as news_status
                    FROM news_title nt 
                    INNER JOIN news n ON n.id = nt.news_id
                    WHERE nt.id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting news by id: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy bài viết theo news_id
     */
    public function getByNewsId($newsId)
    {
        try {
            $sql = "SELECT nt.*, n.category_id, n.author 
                    FROM news_title nt 
                    INNER JOIN news n ON n.id = nt.news_id
                    WHERE nt.news_id = :news_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':news_id' => $newsId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting news by news_id: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Tạo bài viết mới trong bảng news
     */
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
                ':status' => ($data['status'] === 'published' || $data['status'] === 'scheduled') ? 1 : 0
            ]);

            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating news: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tạo chi tiết bài viết trong bảng news_title
     */
    public function createNewsTitle($newsId, $data)
    {
        try {
            $sql = "INSERT INTO news_title (
                        news_id, title, slug, description, content, 
                        meta_title, meta_description, meta_keywords,
                        featured_image, featured_image_caption,
                        video_url, audio_url, gallery_images,
                        source, source_url, author_note,
                        reading_time, is_featured, is_breaking, is_hot,
                        views, share_count, like_count, comment_count,
                        status, published_at, scheduled_at, created_at, updated_at
                    ) VALUES (
                        :news_id, :title, :slug, :description, :content,
                        :meta_title, :meta_description, :meta_keywords,
                        :featured_image, :featured_image_caption,
                        :video_url, :audio_url, :gallery_images,
                        :source, :source_url, :author_note,
                        :reading_time, :is_featured, :is_breaking, :is_hot,
                        :views, :share_count, :like_count, :comment_count,
                        :status, :published_at, :scheduled_at, NOW(), NOW()
                    )";

            $stmt = $this->conn->prepare($sql);

            // Xử lý published_at
            $publishedAt = null;
            if ($data['status'] === 'published') {
                $publishedAt = !empty($data['published_at']) ? $data['published_at'] : date('Y-m-d H:i:s');
            }

            // Xử lý scheduled_at
            $scheduledAt = null;
            if ($data['status'] === 'scheduled' && !empty($data['scheduled_at'])) {
                $scheduledAt = $data['scheduled_at'];
            }

            $stmt->execute([
                ':news_id' => $newsId,
                ':title' => $data['title'],
                ':slug' => $data['slug'],
                ':description' => $data['description'] ?? null,
                ':content' => $data['content'] ?? null,
                ':meta_title' => $data['meta_title'] ?? null,
                ':meta_description' => $data['meta_description'] ?? null,
                ':meta_keywords' => $data['meta_keywords'] ?? null,
                ':featured_image' => $data['featured_image'] ?? null,
                ':featured_image_caption' => $data['featured_image_caption'] ?? null,
                ':video_url' => $data['video_url'] ?? null,
                ':audio_url' => $data['audio_url'] ?? null,
                ':gallery_images' => $data['gallery_images'] ?? null,
                ':source' => $data['source'] ?? null,
                ':source_url' => $data['source_url'] ?? null,
                ':author_note' => $data['author_note'] ?? null,
                ':reading_time' => $data['reading_time'] ?? $this->calculateReadingTime($data['content'] ?? ''),
                ':is_featured' => isset($data['is_featured']) ? (int)$data['is_featured'] : 0,
                ':is_breaking' => isset($data['is_breaking']) ? (int)$data['is_breaking'] : 0,
                ':is_hot' => isset($data['is_hot']) ? (int)$data['is_hot'] : 0,
                ':views' => $data['views'] ?? 0,
                ':share_count' => $data['share_count'] ?? 0,
                ':like_count' => $data['like_count'] ?? 0,
                ':comment_count' => $data['comment_count'] ?? 0,
                ':status' => $data['status'],
                ':published_at' => $publishedAt,
                ':scheduled_at' => $scheduledAt
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Error creating news title: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật bài viết
     */
    public function updateNews($newsId, $data)
    {
        try {
            // Cập nhật bảng news
            $sql = "UPDATE news SET 
                        category_id = :category_id,
                        author = :author,
                        updated_at = NOW()
                    WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':category_id' => $data['category_id'],
                ':author' => $data['author_name'],
                ':id' => $newsId
            ]);

            // Cập nhật bảng news_title
            $sql = "UPDATE news_title SET 
                        title = :title,
                        slug = :slug,
                        description = :description,
                        content = :content,
                        meta_title = :meta_title,
                        meta_description = :meta_description,
                        meta_keywords = :meta_keywords,
                        featured_image = :featured_image,
                        featured_image_caption = :featured_image_caption,
                        video_url = :video_url,
                        audio_url = :audio_url,
                        gallery_images = :gallery_images,
                        source = :source,
                        source_url = :source_url,
                        author_note = :author_note,
                        reading_time = :reading_time,
                        is_featured = :is_featured,
                        is_breaking = :is_breaking,
                        is_hot = :is_hot,
                        status = :status,
                        published_at = :published_at,
                        scheduled_at = :scheduled_at,
                        updated_at = NOW()
                    WHERE news_id = :news_id";

            $stmt = $this->conn->prepare($sql);
            
            // Xử lý published_at
            $publishedAt = null;
            if ($data['status'] === 'published') {
                $publishedAt = !empty($data['published_at']) ? $data['published_at'] : date('Y-m-d H:i:s');
            }

            $scheduledAt = null;
            if ($data['status'] === 'scheduled' && !empty($data['scheduled_at'])) {
                $scheduledAt = $data['scheduled_at'];
            }

            $stmt->execute([
                ':title' => $data['title'],
                ':slug' => $data['slug'],
                ':description' => $data['description'] ?? null,
                ':content' => $data['content'] ?? null,
                ':meta_title' => $data['meta_title'] ?? null,
                ':meta_description' => $data['meta_description'] ?? null,
                ':meta_keywords' => $data['meta_keywords'] ?? null,
                ':featured_image' => $data['featured_image'] ?? null,
                ':featured_image_caption' => $data['featured_image_caption'] ?? null,
                ':video_url' => $data['video_url'] ?? null,
                ':audio_url' => $data['audio_url'] ?? null,
                ':gallery_images' => $data['gallery_images'] ?? null,
                ':source' => $data['source'] ?? null,
                ':source_url' => $data['source_url'] ?? null,
                ':author_note' => $data['author_note'] ?? null,
                ':reading_time' => $data['reading_time'] ?? $this->calculateReadingTime($data['content'] ?? ''),
                ':is_featured' => isset($data['is_featured']) ? (int)$data['is_featured'] : 0,
                ':is_breaking' => isset($data['is_breaking']) ? (int)$data['is_breaking'] : 0,
                ':is_hot' => isset($data['is_hot']) ? (int)$data['is_hot'] : 0,
                ':status' => $data['status'],
                ':published_at' => $publishedAt,
                ':scheduled_at' => $scheduledAt,
                ':news_id' => $newsId
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Error updating news: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa bài viết
     */
    public function deleteNews($newsId)
    {
        try {
            // Xóa từ bảng news_title (FOREIGN KEY CASCADE sẽ xóa news tự động)
            // Nhưng để an toàn, xóa từ news_title trước
            $sql = "DELETE FROM news_title WHERE news_id = :news_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':news_id' => $newsId]);
            
            // Xóa từ bảng news
            $sql = "DELETE FROM news WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $newsId]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Error deleting news: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($newsId, $status)
    {
        try {
            $sql = "UPDATE news_title SET status = :status, updated_at = NOW() WHERE news_id = :news_id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':status' => $status, ':news_id' => $newsId]);
        } catch (PDOException $e) {
            error_log("Error updating status: " . $e->getMessage());
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

    /**
     * Upload ảnh
     */
    public function uploadImage($file, $type = 'featured')
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Chỉ chấp nhận file JPG, PNG, WEBP'];
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'File ảnh không được vượt quá 5MB'];
        }

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/news/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $uploadPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return [
                'success' => true,
                'path' => '/uploads/news/' . $fileName
            ];
        }

        return ['success' => false, 'error' => 'Không thể upload file'];
    }

    /**
     * Tính thời gian đọc
     */
    public function calculateReadingTime($content)
    {
        $text = strip_tags($content);
        // Đếm số từ cho cả tiếng Việt
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        $words = preg_split('/\s+/', trim($text));
        $wordCount = count($words);
        $wordsPerMinute = 200;
        $readingTime = ceil($wordCount / $wordsPerMinute);
        return max(1, $readingTime);
    }

    /**
     * Kiểm tra slug tồn tại
     */
    public function slugExists($slug, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) FROM news_title WHERE slug = :slug";
        $params = [':slug' => $slug];

        if ($excludeId) {
            $sql .= " AND id != :id";
            $params[':id'] = $excludeId;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Thống kê số lượng bài viết theo trạng thái
     */
    public function getStats()
    {
        try {
            $stats = [
                'draft' => 0,
                'published' => 0,
                'scheduled' => 0,
                'archived' => 0,
                'total' => 0
            ];
            
            $sql = "SELECT status, COUNT(*) as count FROM news_title GROUP BY status";
            $stmt = $this->conn->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $row) {
                $stats[$row['status']] = $row['count'];
                $stats['total'] += $row['count'];
            }
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Error getting stats: " . $e->getMessage());
            return ['draft' => 0, 'published' => 0, 'scheduled' => 0, 'archived' => 0, 'total' => 0];
        }
    }
}
?>