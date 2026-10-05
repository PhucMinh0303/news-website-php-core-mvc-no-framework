<?php

/**
 * News Controller - Handles news pages
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../models/NewsModel.php';
require_once __DIR__ . '/../models/NewsTitleModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';


class NewsController extends Controller
{
    public function index($page = 1)
    {
        $this->setPageTitle('Tin tức');

        $newsModel = $this->model('NewsModel');

        $page = max(1, (int) $page);
        $perPage = 7;
        $offset = ($page - 1) * $perPage;

        $newsItems = $newsModel->getPublishedNews($perPage, $offset);
        $totalItems = (int) $newsModel->getTotalPublished();
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        $this->setData('newsItems', $newsItems);
        $this->setData('currentPage', $page);
        $this->setData('totalPages', $totalPages);

        $this->view('News/News');
    }

    public function show($slug)
    {
        $newsModel = $this->model('NewsModel');
        $newsItem = $newsModel->getBySlug($slug);

        if (!is_array($newsItem) && ctype_digit((string) $slug)) {
            $newsItem = $newsModel->getById($slug);

            if (is_array($newsItem) && !empty($newsItem['slug'])) {
                header('Location: ' . Router::url('news/' . rawurlencode($newsItem['slug'])), true, 301);
                exit;
            }
        }

        if (!is_array($newsItem)) {
            http_response_code(404);
            $this->setPageTitle('Tin tức không tồn tại');
            $this->render('errors/404');
            return;
        }

        $newsItem['news_id'] = $newsItem['news_id'] ?? $newsItem['id'] ?? null;
        $newsItem['published_at'] = $newsItem['published_at'] ?? $newsItem['publish_date'] ?? null;
        $newsItem['author_name'] = $newsItem['author_name'] ?? $newsItem['author'] ?? '';
        $newsItem['author_avatar'] = $newsItem['author_avatar'] ?? $newsItem['avatar_img'] ?? null;
        $newsItem['author_bio'] = $newsItem['author_bio'] ?? null;
        $newsItem['description'] = $newsItem['description'] ?? '';
        $newsItem['featured_image'] = $newsItem['featured_image'] ?? $newsItem['image'] ?? null;
        $newsItem['featured_image_caption'] = $newsItem['featured_image_caption'] ?? null;

        $this->setPageTitle($newsItem['title'] ?? 'Chi tiết tin tức');
        $this->setData('news', $newsItem);
        $this->setData('tags', []);
        $this->setData('currentUrl', Router::url('news/' . rawurlencode((string) ($newsItem['slug'] ?? ''))));
        $this->render('News/News-title');
    }
}
