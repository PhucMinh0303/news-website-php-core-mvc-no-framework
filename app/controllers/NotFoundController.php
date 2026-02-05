<?php
/**
 * NotFoundController - Handles 404 errors
 */

class NotFoundController extends Controller {
    
    public function index() {
        $this->setPageTitle('Trang không tìm thấy');
        http_response_code(404);
        $this->render('errors/404');
    }
}

?>
