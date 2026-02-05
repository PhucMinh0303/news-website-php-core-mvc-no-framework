<?php
/**
 * Recruitment Controller - Handles recruitment pages
 */

class RecruitmentController extends Controller {
    
    public function index() {
        $this->setPageTitle('Tuyển dụng');
        
        // In a real app, would load from database
        $this->setData('jobs', [
            ['id' => 1, 'title' => 'Senior Developer'],
            ['id' => 2, 'title' => 'Project Manager'],
        ]);
        
        $this->render('Recruitment/recruitment');
    }
}

?>
