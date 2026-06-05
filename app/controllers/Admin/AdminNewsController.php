<?php
// controllers/admin/AdminNewsController.php
require_once __DIR__ . '/../../models/NewsModel.php';

class AdminNewsController extends Controller
{
    private $newsModel;

    public function __construct()
    {
        $this->newsModel = new NewsTitleModel();
    }

    /**
     * Dashboard - Hiển thị thống kê và danh sách bài viết (Admin)
     */
    public function index()
    {
        // Lấy thống kê số lượng theo trạng thái
        $stats = $this->newsModel->getStats();
        
        // Lấy danh sách bài viết
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;

        $articles = $this->newsModel->getAllAdmin($status, $limit, $offset, $search);
        $total = $this->newsModel->countAdmin($status, $search);
        $totalPages = ceil($total / $limit);

        $data = [
            'stats' => $stats,
            'articles' => $articles,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_records' => $total,
            'status_filter' => $status,
            'search_keyword' => $search,
            'page' => $page
        ];

        $this->view('admin/main/news/news_admin', $data);
    }

    /**
     * Form tạo bài viết mới
     */
    public function create()
    {
        // Lấy danh sách categories và authors
        $categories = $this->newsModel->getCategories();
        $authors = $this->newsModel->getAuthors();
        
        $data = [
            'categories' => $categories,
            'authors' => $authors
        ];
        
        $this->view('admin/main/news/create', $data);
    }

    /**
     * Xử lý lưu bài viết mới
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/main/news');
            return;
        }

        // Validate dữ liệu
        $errors = [];
        if (empty($_POST['title'])) {
            $errors[] = 'Tiêu đề bài viết không được để trống';
        }
        if (empty($_POST['category_id'])) {
            $errors[] = 'Vui lòng chọn chuyên mục';
        }
        if (empty($_POST['author_name'])) {
            $errors[] = 'Tên tác giả không được để trống';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /admin/main/news/create');
            return;
        }

        // Xử lý upload ảnh đại diện
        $featuredImage = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage = $this->newsModel->uploadImage($_FILES['featured_image']);
            if ($uploadedImage['success']) {
                $featuredImage = $uploadedImage['path'];
            }
        }

        // Tạo slug từ tiêu đề nếu chưa có
        $slug = !empty($_POST['slug']) ? $_POST['slug'] : $this->newsModel->createSlug($_POST['title']);
        
        // Xác định status và action
        $action = $_POST['action'] ?? 'draft';
        $status = $_POST['status'] ?? 'draft';
        
        // Nếu click nút "Đăng bài" thì chuyển thành published
        if ($action === 'publish') {
            $status = 'published';
        }
        
        // Nếu status là draft và click nút "Đăng bài" thì publish
        if ($status === 'draft' && $action === 'publish') {
            $status = 'published';
        }

        // Chuẩn bị dữ liệu cho bảng news
        $newsData = [
            'category_id' => $_POST['category_id'],
            'author_name' => $_POST['author_name'],
            'views' => (int)($_POST['views'] ?? 0),
            'status' => $status
        ];

        // Tạo bản ghi trong bảng news
        $newsId = $this->newsModel->createNews($newsData);
        
        if (!$newsId) {
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo bài viết!';
            header('Location: /admin/main/news/create');
            return;
        }

        // Chuẩn bị dữ liệu cho bảng news_title
        $content = $_POST['content'] ?? '';
        
        $newsTitleData = [
            'title' => $_POST['title'],
            'slug' => $slug,
            'description' => $_POST['description'] ?? '',
            'content' => $content,
            'meta_title' => $_POST['meta_title'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'meta_keywords' => $_POST['meta_keywords'] ?? '',
            'featured_image' => $featuredImage,
            'featured_image_caption' => $_POST['featured_image_caption'] ?? '',
            'video_url' => $_POST['video_url'] ?? null,
            'audio_url' => $_POST['audio_url'] ?? null,
            'gallery_images' => $_POST['gallery_images'] ?? null,
            'source' => $_POST['source'] ?? '',
            'source_url' => $_POST['source_url'] ?? '',
            'author_note' => $_POST['author_note'] ?? '',
            'reading_time' => isset($_POST['reading_time']) ? (int)$_POST['reading_time'] : $this->newsModel->calculateReadingTime($content),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_breaking' => isset($_POST['is_breaking']) ? 1 : 0,
            'is_hot' => isset($_POST['is_hot']) ? 1 : 0,
            'views' => (int)($_POST['views'] ?? 0),
            'share_count' => 0,
            'like_count' => 0,
            'comment_count' => 0,
            'status' => $status,
            'published_at' => $_POST['published_at'] ?? null,
            'scheduled_at' => $_POST['scheduled_at'] ?? null
        ];

        // Tạo bản ghi trong bảng news_title
        $result = $this->newsModel->createNewsTitle($newsId, $newsTitleData);

        if ($result) {
            $_SESSION['success'] = $status === 'published' ? 'Đăng bài viết thành công!' : 'Lưu bản nháp thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
        }

        header('Location: /admin/main/news');
    }

    /**
     * Form chỉnh sửa bài viết
     */
    public function edit($id)
    {
        $article = $this->newsModel->getById($id);
        
        if (!$article) {
            header('HTTP/1.0 404 Not Found');
            $this->view('errors/404');
            return;
        }
        
        $categories = $this->newsModel->getCategories();
        $authors = $this->newsModel->getAuthors();
        
        $data = [
            'article' => $article,
            'categories' => $categories,
            'authors' => $authors
        ];
        
        $this->view('admin/main/news/edit', $data);
    }

    /**
     * Xử lý cập nhật bài viết
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/main/news');
            return;
        }

        $article = $this->newsModel->getById($id);
        if (!$article) {
            $_SESSION['error'] = 'Bài viết không tồn tại!';
            header('Location: /admin/main/news');
            return;
        }

        // Xử lý upload ảnh mới
        $featuredImage = $article['featured_image'];
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage = $this->newsModel->uploadImage($_FILES['featured_image']);
            if ($uploadedImage['success']) {
                $featuredImage = $uploadedImage['path'];
                // Xóa ảnh cũ nếu có
                if ($article['featured_image'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $article['featured_image'])) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $article['featured_image']);
                }
            }
        }

        // Tạo slug nếu tiêu đề thay đổi
        $slug = $article['slug'];
        if ($_POST['title'] !== $article['title']) {
            $slug = !empty($_POST['slug']) ? $_POST['slug'] : $this->newsModel->createSlug($_POST['title']);
        }

        $content = $_POST['content'] ?? '';
        
        $data = [
            'category_id' => $_POST['category_id'],
            'author_name' => $_POST['author_name'],
            'title' => $_POST['title'],
            'slug' => $slug,
            'description' => $_POST['description'] ?? '',
            'content' => $content,
            'meta_title' => $_POST['meta_title'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'meta_keywords' => $_POST['meta_keywords'] ?? '',
            'featured_image' => $featuredImage,
            'featured_image_caption' => $_POST['featured_image_caption'] ?? '',
            'video_url' => $_POST['video_url'] ?? null,
            'audio_url' => $_POST['audio_url'] ?? null,
            'gallery_images' => $_POST['gallery_images'] ?? null,
            'source' => $_POST['source'] ?? '',
            'source_url' => $_POST['source_url'] ?? '',
            'author_note' => $_POST['author_note'] ?? '',
            'reading_time' => isset($_POST['reading_time']) ? (int)$_POST['reading_time'] : $this->newsModel->calculateReadingTime($content),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'is_breaking' => isset($_POST['is_breaking']) ? 1 : 0,
            'is_hot' => isset($_POST['is_hot']) ? 1 : 0,
            'status' => $_POST['status'],
            'published_at' => $_POST['published_at'] ?? null,
            'scheduled_at' => $_POST['scheduled_at'] ?? null
        ];

        $result = $this->newsModel->updateNews($article['news_id'], $data);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật bài viết thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
        }

        header('Location: /admin/main/news');
    }

    /**
     * Xóa bài viết
     */
    public function destroy($id)
    {
        $article = $this->newsModel->getById($id);

        if ($article) {
            // Xóa ảnh đại diện nếu có
            if ($article['featured_image'] && file_exists($_SERVER['DOCUMENT_ROOT'] . $article['featured_image'])) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $article['featured_image']);
            }

            $result = $this->newsModel->deleteNews($article['news_id']);
            $_SESSION['success'] = $result ? 'Xóa bài viết thành công!' : 'Xóa bài viết thất bại!';
        } else {
            $_SESSION['error'] = 'Bài viết không tồn tại!';
        }

        header('Location: /admin/main/news');
    }

    /**
     * Cập nhật trạng thái (bật/tắt)
     */
    public function toggleStatus($id)
    {
        $article = $this->newsModel->getById($id);

        if ($article) {
            $newStatus = '';
            switch ($article['status']) {
                case 'draft':
                    $newStatus = 'published';
                    break;
                case 'published':
                    $newStatus = 'archived';
                    break;
                case 'archived':
                    $newStatus = 'draft';
                    break;
                default:
                    $newStatus = 'draft';
            }
            
            $result = $this->newsModel->updateStatus($article['news_id'], $newStatus);
            
            if ($result) {
                $statusText = '';
                switch ($newStatus) {
                    case 'published':
                        $statusText = 'Đã đăng';
                        break;
                    case 'archived':
                        $statusText = 'Đã lưu trữ';
                        break;
                    default:
                        $statusText = 'Bản nháp';
                }
                $_SESSION['success'] = "Đã chuyển trạng thái sang {$statusText}!";
            } else {
                $_SESSION['error'] = 'Cập nhật trạng thái thất bại!';
            }
        }

        header('Location: /admin/main/news');
    }
}
?>