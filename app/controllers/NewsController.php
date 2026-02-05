<?php
/**
 * News Controller - Handles news pages
 */

class NewsController extends Controller {
    
    public function index() {
        $this->setPageTitle('Tin tức');
        
        // In a real app, would load from database via NewsModel
        $this->setData('newsItems', [
            ['id' => 1, 'title' => 'Tin tức 1'],
            ['id' => 2, 'title' => 'Tin tức 2'],
        ]);
        
        $this->render('News/News');
    }
    
    public function show($id) {
        // Load specific news item
        $this->setPageTitle('Chi tiết tin tức');
        $this->setData('newsId', $id);
        $this->render('News/News-title');
    }
}

?>
