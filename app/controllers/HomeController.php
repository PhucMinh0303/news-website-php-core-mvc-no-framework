<?php
/**
 * Home Controller - Handles homepage
 */

class HomeController extends Controller {
    
    public function index() {
        $this->setPageTitle('Trang chủ');
        
        // Set data for homepage
        $this->setData('sections', [
            'section1',
            'section2',
            'section3-2',
            'section4',
            'section5'
        ]);
        $this->setData('animation_data', [
            'element' => '.some-element',
            'animation' => 'fadeIn',
            'duration' => 1000
        ]);
        
        
        
        $this->render('homepage/homepage');
        
        
    }
    
}

?>
