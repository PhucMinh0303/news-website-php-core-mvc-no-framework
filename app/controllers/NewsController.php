<?php
// controllers/NewsController.php

require_once '../core/Controller.php';
require_once '../models/NewsModel.php';
require_once '../models/NewsTitleModel.php';
require_once '../models/CategoryModel.php';

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

    /**
     * Trang danh sách bài viết
     */
    public function index($page = 1)
    {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // Lấy danh sách bài viết
        $featuredNews = $this->newsModel->getFeaturedNews(2); // 2 bài featured đầu
        $latestNews = $this->newsModel->getPublishedNews($limit, $offset);
        $totalNews = count($this->newsModel->getPublishedNews());
        $totalPages = ceil($totalNews / $limit);

        // Lấy danh sách category cho sidebar
        $categories = $this->categoryModel->getWithNewsCount();

        $data = [
            'featuredNews' => $featuredNews,
            'latestNews' => $latestNews,
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'title' => 'Tin tức - EMIR',
            'description' => 'Cập nhật tin tức tài chính mới nhất'
        ];

        $this->view('news/news', $data);
    }

    /**
     * Chi tiết bài viết
     */
    public function detail($slug)
    {
        // Lấy chi tiết bài viết từ news_title
        $news = $this->newsTitleModel->getNewsDetail($slug);

        if (!$news) {
            header('HTTP/1.0 404 Not Found');
            $this->view('errors/404');
            return;
        }

        // Tăng lượt xem
        $this->newsModel->incrementViews($news['news_id']);
        $this->newsTitleModel->incrementViews($news['id']);

        // Lấy tags cho bài viết (nếu có)
        $tags = $this->newsTitleModel->getTagsByNewsId($news['news_id']);

        // Lấy bài viết liên quan
        $relatedNews = $this->newsTitleModel->getRelatedNews(
            $news['news_id'],
            $news['category_id'],
            5
        );

        // Current URL for sharing
        $currentUrl = $this->getCurrentUrl();

        $data = [
            'news' => $news,
            'tags' => $tags,
            'relatedNews' => $relatedNews,
            'currentUrl' => $currentUrl,
            'title' => $news['meta_title'] ?? $news['title'],
            'description' => $news['meta_description'] ?? $news['description']
        ];

        $this->view('news/news-title', $data);
    }

    /**
     * Lấy URL hiện tại
     */
    private function getCurrentUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
}