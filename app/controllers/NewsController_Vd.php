<?php
/**
 * News Controller - Handles news pages
 */
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/News_model.php';
require_once __DIR__ . '/../models/Category_model.php';

class NewsController extends Controller
{
    private $newsModel;
    private $categoryModel;

    public function __construct()
    {
        $this->newsModel = new NewsModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $this->setPageTitle('Tin tức');
        $perPage = 10;

        // In a real app, would load from database via NewsModel
        $this->setData('newsItems', [
            ['id' => 1, 'title' => 'Tin tức 1'],
            ['id' => 2, 'title' => 'Tin tức 2'],
        ]);

        $this->render('News/News');
    }

    public function show($slug)
    {
        // Load specific news item
        $this->setPageTitle('Chi tiết tin tức');
        $this->setData('newsId', $slug);
        $this->render('News/News-title');

        $news = $this->newsModel->getNewsBySlug($slug);
        if (!$news) {
            $this->view('errors/404');
            return;
        }
        // Get related news
        $relatedNews = $this->newsModel->getRelatedNews($news['id'], $news['category_id'], 5);

        // Get category
        $category = $this->categoryModel->findById($news['category_id']);

        $this->view('news/show', [
            'news' => $news,
            'category' => $category,
            'relatedNews' => $relatedNews
        ]);
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
}

?>
