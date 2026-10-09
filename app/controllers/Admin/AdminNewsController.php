<?php
// controllers/admin/AdminNewsController.php

require_once __DIR__ . '/../../models/NewsModel.php';
require_once __DIR__ . '/../../models/NewsTitleModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';
require_once __DIR__ . '/../../services/FileUploadService.php';

class AdminNewsController extends Controller
{
    private $newsModel;
    private $fileUploadService;

    public function __construct()
    {
        $this->newsModel = new NewsModel();
        $this->fileUploadService = new FileUploadService();
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

        if ($this->isAjax()) {
            $this->view('admin/main/news/news_admin', $data);
            return;
        }

        $this->view('admin/admin', array_merge($data, [
            'admin_view' => 'admin/main/news/news_admin',
            'admin_page' => 'news'
        ]));
    }

    /**
     * Form tạo bài viết mới
     */
    public function create()
    {
        // Lấy danh sách categories và authors cho form
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getAllCategories();
        $authors = $this->newsModel->getAuthors();

        $data = [
            'categories' => $categories,
            'authors' => $authors,
            'admin_layout' => true
        ];

        if ($this->isAjax()) {
            $this->view('admin/main/news/create-news', $data);
            return;
        }

        $this->view('admin/admin', array_merge($data, [
            'admin_view' => 'admin/main/news/create-news',
            'admin_page' => 'news'
        ]));
    }

    /**
     * Upload ảnh chèn trong nội dung bài viết, trả về URL để chèn vào editor
     */
    public function uploadContentImage()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method không hợp lệ.'], 405);
        }

        $file = $_FILES['image'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'Không nhận được tệp ảnh hợp lệ.'], 400);
        }
        if ($file['size'] > 5 * 1024 * 1024) {
            $this->json(['success' => false, 'message' => 'Ảnh không được vượt quá 5MB.'], 400);
        }

        $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!isset($extensions[$mime])) {
            $this->json(['success' => false, 'message' => 'Chỉ hỗ trợ ảnh JPG, PNG, WEBP.'], 400);
        }

        $dir = __DIR__ . '/../../../public/upload/news-content/';
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
            $this->json(['success' => false, 'message' => 'Không thể tạo thư mục lưu ảnh.'], 500);
        }

        $filename = date('YmdHis') . '_' . bin2hex(random_bytes(6)) . '.' . $extensions[$mime];
        if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            $this->json(['success' => false, 'message' => 'Không thể lưu ảnh.'], 500);
        }

        $this->json(['success' => true, 'url' => BASE_URL . 'public/upload/news-content/' . $filename]);
    }

    /**
     * Xử lý lưu bài viết mới
     */
    public function store($routeSlug)
    {
        // Kiểm tra method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . Router::url('admin/news'));
            exit;
        }

        // 1. Validate dữ liệu
        $errors = [];
        $oldInput = [];

        // Validate title
        if (empty($_POST['title'])) {
            $errors[] = 'Tiêu đề bài viết là bắt buộc.';
        } elseif (strlen($_POST['title']) > 255) {
            $errors[] = 'Tiêu đề không được vượt quá 255 ký tự.';
        } else {
            $oldInput['title'] = $_POST['title'];
        }

        // Validate slug
        if (empty($routeSlug)) {
            // Tự động tạo slug từ title nếu không có
            $slug = $this->newsModel->createSlug($_POST['title'] ?? '');
            $slug = $this->newsModel->generateUniqueSlug($slug);
        } else {
            $slug = strtolower($routeSlug);
            // Kiểm tra slug đã tồn tại
            if ($this->newsModel->slugExists($slug)) {
                $slug = $this->newsModel->generateUniqueSlug($slug);
            }
        }
        $oldInput['slug'] = $slug;

        // Validate category
        if (empty($_POST['category_id'])) {
            $errors[] = 'Vui lòng chọn danh mục.';
        } else {
            $oldInput['category_id'] = $_POST['category_id'];
        }

        // Validate author
        if (empty($_POST['author'])) {
            $errors[] = 'Tên tác giả hiển thị là bắt buộc.';
        } elseif (strlen($_POST['author']) > 100) {
            $errors[] = 'Tên tác giả không được vượt quá 100 ký tự.';
        } else {
            $oldInput['author'] = $_POST['author'];
        }

        // Validate publish_date
        if (empty($_POST['publish_date'])) {
            $errors[] = 'Ngày đăng là bắt buộc.';
        } else {
            $oldInput['publish_date'] = $_POST['publish_date'];
        }

        // Validate content
        if (empty($_POST['content'])) {
            $errors[] = 'Nội dung bài viết là bắt buộc.';
        } else {
            $oldInput['content'] = $_POST['content'];
        }

        // Validate status
        $status = $_POST['status'] ?? 'draft';
        if (isset($_POST['publish'])) {
            $status = 'published';
        } elseif (isset($_POST['save_draft'])) {
            $status = 'draft';
        }
        if (!in_array($status, ['draft', 'published', 'archived'])) {
            $errors[] = 'Trạng thái không hợp lệ.';
        }
        $oldInput['status'] = $status;

        // Validate featured_image (nếu có upload)
        $imagePath = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['featured_image']['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Lỗi khi tải lên ảnh đại diện.';
            } else {
                // Kiểm tra file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                $fileType = mime_content_type($_FILES['featured_image']['tmp_name']);
                if (!in_array($fileType, $allowedTypes)) {
                    $errors[] = 'Ảnh đại diện phải có định dạng JPG, PNG hoặc WEBP.';
                }
                // Kiểm tra kích thước (15MB = 15 * 1024 * 1024)
                if ($_FILES['featured_image']['size'] > 15 * 1024 * 1024) {
                    $errors[] = 'Kích thước ảnh không được vượt quá 15MB.';
                }
            }
        }

        // 2. Nếu có lỗi, redirect về form với thông báo
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $oldInput;
            header('Location: ' . Router::url('admin/news/create'));
            exit;
        }

        // 3. Xử lý upload ảnh đại diện
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $this->fileUploadService->upload(
                $_FILES['featured_image'],
                'news'
            );

            if ($uploadResult['success']) {
                $imagePath = $uploadResult['filename'];
            } else {
                $errors[] = $uploadResult['message'] ?? 'Không thể tải lên ảnh đại diện.';
                $_SESSION['errors'] = $errors;
                $_SESSION['old_input'] = $oldInput;
                header('Location: ' . Router::url('admin/news/create'));
                exit;
            }
        }

        // 4. Lưu vào database
        $newsId = $this->newsModel->create([
            'title' => $_POST['title'],
            'slug' => $slug,
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'author_id' => !empty($_POST['author_id']) ? (int)$_POST['author_id'] : null,
            'author' => $_POST['author'],
            'content' => $_POST['content'],
            'image' => $imagePath,
            'publish_date' => $_POST['publish_date'],
            'status' => $status,
            'views' => 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // 5. Redirect về danh sách với thông báo thành công
        if ($newsId) {
            $statusText = ($status === 'published') ? 'đăng' : 'lưu';
            $_SESSION['success'] = "Bài viết đã được {$statusText} thành công!";
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi lưu bài viết, vui lòng thử lại.';
        }

        if ($newsId && isset($_POST['save_and_continue']) && $_POST['save_and_continue'] == 1) {
            header('Location: ' . Router::url('admin/news', [$slug]));
            exit;
        }

        header('Location: ' . Router::url('admin/news'));
        exit;
    }

    /**
     * Form chỉnh sửa bài viết
     */
    public function editBySlug($slug)
    {
        $article = $this->newsModel->getBySlug($slug);

        if (!$article) {
            header('HTTP/1.0 404 Not Found');
            $this->view('errors/404');
            return;
        }

        $this->edit($article['id']);
    }

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

        $data['edit_id'] = $id;
        $this->view('admin/admin', array_merge($data, [
            'admin_view' => 'admin/main/news/edit-news',
            'admin_page' => 'news',
            'admin_layout' => true
        ]));
    }

    /**
     * Xử lý cập nhật bài viết
     */
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . Router::url('admin/news'));
            exit;
        }

        $article = $this->newsModel->getById($id);
        if (!$article) {
            $_SESSION['error'] = 'Bài viết không tồn tại!';
            header('Location: ' . Router::url('admin/news'));
            exit;
        }

        // Validate dữ liệu
        $errors = [];

        if (empty($_POST['title'])) {
            $errors[] = 'Tiêu đề bài viết là bắt buộc.';
        } elseif (strlen($_POST['title']) > 255) {
            $errors[] = 'Tiêu đề không được vượt quá 255 ký tự.';
        }

        if (empty($_POST['author'])) {
            $errors[] = 'Tên tác giả là bắt buộc.';
        }

        if (empty($_POST['content'])) {
            $errors[] = 'Nội dung bài viết là bắt buộc.';
        }

        // Xử lý slug nếu tiêu đề thay đổi
        $slug = $article['slug'];
        if ($_POST['title'] !== $article['title']) {
            if (!empty($_POST['slug'])) {
                $slug = $_POST['slug'];
            } else {
                $slug = $this->newsModel->createSlug($_POST['title']);
            }
            $slug = $this->newsModel->generateUniqueSlug($slug, $id);
        }

        // Xử lý upload ảnh mới
        $featuredImage = $article['featured_image'] ?? $article['image'] ?? null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileType = mime_content_type($_FILES['featured_image']['tmp_name']);

            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = 'Ảnh đại diện phải có định dạng JPG, PNG hoặc WEBP.';
            } elseif ($_FILES['featured_image']['size'] > 15 * 1024 * 1024) {
                $errors[] = 'Kích thước ảnh không được vượt quá 15MB.';
            } else {
                // Upload ảnh mới
                $uploadResult = $this->fileUploadService->upload(
                    $_FILES['featured_image'],
                    'news'
                );

                if ($uploadResult['success']) {
                    // Xóa ảnh cũ
                    if ($featuredImage) {
                        $this->fileUploadService->delete('news', basename($featuredImage));
                    }
                    $featuredImage = $uploadResult['filename'];
                } else {
                    $errors[] = $uploadResult['message'] ?? 'Không thể tải lên ảnh đại diện.';
                }
            }
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: ' . Router::url('admin/news/edit', [$id]));
            exit;
        }

        // Cập nhật dữ liệu
        $newsData = [
            'title' => $_POST['title'],
            'slug' => $slug,
            'category_id' => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
            'author_id' => !empty($_POST['author_id']) ? (int)$_POST['author_id'] : null,
            'author' => $_POST['author'],
            'publish_date' => !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d'),
            'image' => $featuredImage,
            'content' => $_POST['content'],
            'status' => $_POST['status'] ?? 'draft',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $result = $this->newsModel->update($id, $newsData);

        if ($result) {
            $_SESSION['success'] = 'Cập nhật bài viết thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra, vui lòng thử lại!';
        }

        if ($result && isset($_POST['save_and_continue']) && $_POST['save_and_continue'] == 1) {
            header('Location: ' . Router::url('admin/news/edit', [$id]));
            exit;
        }

        header('Location: ' . Router::url('admin/news'));
        exit;
    }

    /**
     * Xóa bài viết
     */
    public function destroy($id)
    {
        $article = $this->newsModel->getById($id);

        if ($article) {
            // Xóa ảnh đại diện nếu có
            if (!empty($article['featured_image']) || !empty($article['image'])) {
                $imagePath = $article['featured_image'] ?? $article['image'] ?? null;
                if ($imagePath) {
                    $this->fileUploadService->delete('news', basename($imagePath));
                }
            }

            $result = $this->newsModel->delete($id);
            $_SESSION['success'] = $result ? 'Xóa bài viết thành công!' : 'Xóa bài viết thất bại!';
        } else {
            $_SESSION['error'] = 'Bài viết không tồn tại!';
        }

        header('Location: ' . Router::url('admin/news'));
        exit;
    }

    /**
     * Cập nhật trạng thái (xoay vòng: draft → published → archived → draft)
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

        header('Location: ' . Router::url('admin/news'));
        exit;
    }
}
