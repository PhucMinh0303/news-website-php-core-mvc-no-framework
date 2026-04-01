<?php
// app/models/NewsModel.php

require_once __DIR__ . '/../core/Model.php';

class NewsModel extends Model
{
    protected $table = 'news';

    public function getAllPublished($limit = 10, $offset = 0)
    {
        $sql = "SELECT n.*, c.name as category_name 
                FROM news n
                LEFT JOIN categories c ON n.category_id = c.id
                WHERE n.status = 'published'
                ORDER BY n.publish_date DESC
                LIMIT ? OFFSET ?";

        return $this->query($sql, [$limit, $offset], 'ii');
    }

    public function getNewsWithDetails($slug)
    {
        $sql = "SELECT n.*, c.name as category_name, a.full_name as author_name,
                nt.title as seo_title, nt.meta_description, nt.content as full_content
                FROM news n
                LEFT JOIN categories c ON n.category_id = c.id
                LEFT JOIN authors a ON n.author_id = a.id
                LEFT JOIN news_title nt ON n.id = nt.news_id
                WHERE n.slug = ? AND n.status = 'published'";

        $result = $this->query($sql, [$slug], 's');
        return $result[0] ?? null;
    }
}

?>