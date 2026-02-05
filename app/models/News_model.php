<?php
/**
 * News Model - Handles news data
 */

namespace App\Controllers\Api;

use App\Core\Database;


class News extends Model {
    
    protected static $data = [
        [
            'id' => 1,
            'title' => 'Capital AM mở rộng dịch vụ',
            'slug' => 'capital-am-mo-rong-dich-vu',
            'excerpt' => 'Thông báo mở rộng dịch vụ...',
            'content' => 'Nội dung đầy đủ...',
            'date' => '2026-01-09',
            'author' => 'Admin',
        ],
        [
            'id' => 2,
            'title' => 'Thành công trong M&A',
            'slug' => 'thanh-cong-trong-ma',
            'excerpt' => 'Chúng tôi vừa hoàn thành...',
            'content' => 'Nội dung đầy đủ...',
            'date' => '2026-01-08',
            'author' => 'Admin',
        ],
    ];
    
    /**
     * Get recent news
     */
    public static function getRecent($limit = 5) {
        return array_slice(static::$data, 0, $limit);
    }
}

?>
