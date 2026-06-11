<?php
// controllers/admin/AdminNewsController.php

require_once __DIR__ . '/../../models/NewsModel.php';

class AdminNewsController extends Controller
{
    private $newsModel;

    public function __construct()
    {
        $this->newsModel = new NewsModel();
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
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;
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
        if (empty($_POST['content'])) {
            $errors[] = 'Nội dung bài viết không được để trống';
        }
        if (empty($_POST['author'])) {
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

        // Tạo slug
        $slug = !empty($_POST['slug']) ? $_POST['slug'] : $this->newsModel->createSlug($_POST['title']);
        $slug = $this->newsModel->generateUniqueSlug($slug);
        
        // Xác định status
        $action = $_POST['action'] ?? 'draft';
        $status = $_POST['status'] ?? 'draft';
        
        if ($action === 'publish') {
            $status = 'published';
        }

        // Chuẩn bị dữ liệu
        $newsData = [
            'title' => $_POST['title'],
            'slug' => $slug,
            'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
            'author_id' => !empty($_POST['author_id']) ? $_POST['author_id'] : null,
            'author' => $_POST['author'],
            'publish_date' => !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d'),
            'image' => $featuredImage,
            'content' => $_POST['content'],
            'views' => (int)($_POST['views'] ?? 0),
            'status' => $status
        ];

        // Tạo bài viết
        $newsId = $this->newsModel->create($newsData);

        if ($newsId) {
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
        $featuredImage = $article['image'];
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadedImage = $this->newsModel->uploadImage($_FILES['featured_image']);
            if ($uploadedImage['success']) {
                // Xóa ảnh cũ
                if ($article['image']) {
                    $this->newsModel->deleteImage($article['image']);
                }
                $featuredImage = $uploadedImage['path'];
            }
        }

        // Tạo slug nếu tiêu đề thay đổi
        $slug = $article['slug'];
        if ($_POST['title'] !== $article['title']) {
            $slug = !empty($_POST['slug']) ? $_POST['slug'] : $this->newsModel->createSlug($_POST['title']);
            $slug = $this->newsModel->generateUniqueSlug($slug, $id);
        }

        // Chuẩn bị dữ liệu
        $newsData = [
            'title' => $_POST['title'],
            'slug' => $slug,
            'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
            'author_id' => !empty($_POST['author_id']) ? $_POST['author_id'] : null,
            'author' => $_POST['author'],
            'publish_date' => !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d'),
            'image' => $featuredImage,
            'content' => $_POST['content'],
            'status' => $_POST['status'] ?? 'draft'
        ];

        $result = $this->newsModel->update($id, $newsData);

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
            if ($article['image']) {
                $this->newsModel->deleteImage($article['image']);
            }

            $result = $this->newsModel->delete($id);
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
            
            $result = $this->newsModel->updateStatus($id, $newStatus);
            
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