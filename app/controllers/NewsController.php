<?php
// controllers/NewsController.php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/NewsModel.php';
require_once __DIR__ . '/../models/NewsTitleModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';


class NewsController extends Controller
{
    private $newsModel;
    private $newsTitleModel;
    private $categoryModel;

    public function __construct()
    {
        $this->newsModel = new NewsModel();
        $this->newsTitleModel = new NewsTitleModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $perPage = 10;

        $result = $this->newsModel->paginate($page, $perPage, ['status' => 'published']);

        $this->view('News/News', [
            'news' => $result['data'],
            'currentPage' => $page,
            'totalPages' => $result['totalPages'],
            'total' => $result['total']
        ]);
    }

    /**
     * Hiển thị chi tiết tin tức
     * @param string $slug - Slug của bài viết
     */
    public function show($slug)
    {
        // Lấy chi tiết bài viết từ slug
        $news = $this->newsTitleModel->getNewsDetailBySlug($slug);

        // Kiểm tra bài viết tồn tại
        if (!$news) {
            $this->view('errors/404');
            return;
        }

        // Lấy tags của bài viết
        $tags = $this->newsTitleModel->getTagsByNewsId($news['news_id']);

        // Lấy comments
        $comments = $this->newsTitleModel->getCommentsByNewsId($news['news_id'], true);

        // Lấy bài viết liên quan
        $relatedNews = $this->newsTitleModel->getRelatedNews($news['news_id'], 5);

        // Lấy bài viết phổ biến
        $popularNews = $this->newsTitleModel->getPopularNews(5);

        // Lấy bài viết trước và sau
        $previousNews = $this->newsTitleModel->getPreviousNews($news['news_id'], $news['publish_date']);
        $nextNews = $this->newsTitleModel->getNextNews($news['news_id'], $news['publish_date']);

        // Xử lý share count (nếu có share từ URL)
        if (isset($_GET['shared']) && $_GET['shared'] == 1) {
            $this->newsTitleModel->updateShareCount($news['news_id']);
        }

        // Xử lý like (nếu có like từ AJAX)
        if (isset($_POST['action']) && $_POST['action'] == 'like') {
            $this->handleLike();
            return;
        }

        // Render view
        $this->view('News/News-title', [
            'news' => $news,
            'tags' => $tags,
            'comments' => $comments,
            'relatedNews' => $relatedNews,
            'popularNews' => $popularNews,
            'previousNews' => $previousNews,
            'nextNews' => $nextNews,
            'baseUrl' => BASE_URL,
            'currentUrl' => BASE_URL . 'news/' . $slug
        ]);
    }

    /**
     * Xử lý like bài viết (AJAX)
     */
    private function handleLike()
    {
        $newsId = $_POST['news_id'] ?? null;
        $action = $_POST['like_action'] ?? 'increment';

        if ($newsId) {
            $result = $this->newsTitleModel->updateLikeCount($newsId, $action === 'increment');
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    /**
     * Xử lý comment
     */
    public function comment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
            return;
        }

        $data = $this->sanitizeInput($_POST);
        $newsId = $data['news_id'] ?? null;

        // Validate
        $errors = $this->validateRequired($data, ['author_name', 'content']);

        if (!empty($errors)) {
            $_SESSION['comment_errors'] = $errors;
            $_SESSION['old_comment'] = $data;
            $this->redirect('/news/' . $data['slug']);
            return;
        }

        // Save comment
        $commentId = $this->newsTitleModel->addComment($newsId, $data);

        if ($commentId) {
            $_SESSION['comment_success'] = 'Bình luận của bạn đã được gửi và đang chờ duyệt!';
        } else {
            $_SESSION['comment_error'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
        }

        $this->redirect('/news/' . $data['slug']);
    }

    public function category($slug)
    {
        $category = $this->categoryModel->getCategoryBySlug($slug);

        if (!$category) {
            $this->view('errors/404');
            return;
        }

        $page = $_GET['page'] ?? 1;
        $perPage = 10;

        $result = $this->newsModel->paginate($page, $perPage, [
            'category_id' => $category['id'],
            'status' => 'published'
        ]);

        $this->view('news/category', [
            'category' => $category,
            'news' => $result['data'],
            'currentPage' => $page,
            'totalPages' => $result['totalPages']
        ]);
    }

    public function search()
    {
        $keyword = $_GET['q'] ?? '';

        if (empty($keyword)) {
            $this->redirect('/');
            return;
        }

        $news = $this->newsModel->searchNews($keyword);

        $this->view('news/search', [
            'keyword' => $keyword,
            'news' => $news
        ]);
    }

    public function tag($slug)
    {
        $tag = $this->newsTitleModel->getTagBySlug($slug);

        if (!$tag) {
            $this->view('errors/404');
            return;
        }

        $page = $_GET['page'] ?? 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT nt.*, n.publish_date 
                FROM news_title nt
                INNER JOIN news n ON nt.news_id = n.id
                INNER JOIN news_tag_relations tr ON n.id = tr.news_id
                WHERE tr.tag_id = ? AND nt.status = 'published'
                ORDER BY n.publish_date DESC
                LIMIT ? OFFSET ?";

        $news = $this->db->fetchAll($sql, [$tag['id'], $perPage, $offset]);

        $this->view('news/tag', [
            'tag' => $tag,
            'news' => $news,
            'currentPage' => $page,
            'perPage' => $perPage
        ]);
    }
}