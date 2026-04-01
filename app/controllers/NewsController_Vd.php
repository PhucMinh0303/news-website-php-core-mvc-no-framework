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
    public function index()
    {
        $this->setPageTitle('Tin tức');

        // In a real app, would load from database via NewsModel
        $this->setData('newsItems', [
            ['id' => 1, 'title' => 'Tin tức 1'],
            ['id' => 2, 'title' => 'Tin tức 2'],
        ]);

        $this->view('News/News');
    }

    public function show($id)
    {
        // Load specific news item
        $this->setPageTitle('Chi tiết tin tức');
        $this->setData('newsId', $id);
        $this->render('News/News-title');
    }
}

?>
