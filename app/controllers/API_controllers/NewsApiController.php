<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\News;

class NewsApiController extends Controller
{
    public function index()
    {
        $newsModel = new News();
        $news = $newsModel->getLatest(5);

        return $this->json([
            'status' => true,
            'data'   => $news
        ]);
    }
}
    public function show($id)
    {
        $newsModel = new News();
        $article = $newsModel->findById($id);

        if ($article) {
            return $this->json([
                'status' => true,
                'data'   => $article
            ]);
        } else {
            return $this->json([
                'status'  => false,
                'message' => 'Article not found'
            ], 404);
        }
    }
}