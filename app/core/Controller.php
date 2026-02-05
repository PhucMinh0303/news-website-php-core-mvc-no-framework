<?php
/**
 * Base Controller Class
 * All controllers inherit from this class
 */

class Controller {
    /**
     * @var array View data
     */
    protected $data = [];
    
    /**
     * @var string View name to render
     */
    protected $view;
    
    /**
     * @var string Layout to use
     */
    protected $layout = 'main';
    
    /**
     * Set page title
     */
    protected function setPageTitle($title) {
        $this->data['page_title'] = $title;
    }
    
    /**
     * Set view data
     */
    protected function setData($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }
    
    /**
     * Set multiple view data
     */
    protected function setDataArray($data) {
        $this->data = array_merge($this->data, $data);
        return $this;
    }
    
    /**
     * Get view data value
     */
    protected function getData($key, $default = null) {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
    
    /**
     * Render a view file
     */
    protected function render($view, $layout = null) {
        $this->view = $view;
        
        if ($layout !== null) {
            $this->layout = $layout;
        }
        
        return $this;
    }
    
    /**
     * Render a view without layout
     */
    protected function renderPartial($view) {
        return $this->render($view, false);
    }
    
    /**
     * Output the view (called after action method)
     */
    public function output() {
        $viewFile = APP_PATH . 'views/' . $this->view . '.php';
        
        if (!file_exists($viewFile)) {
            die("View not found: {$viewFile}");
        }
        
        // Extract data into view scope
        extract($this->data, EXTR_PREFIX_ALL, 'view');
        
        if ($this->layout === false) {
            // Render view only, no layout
            include $viewFile;
        } else {
            // Render with layout
            ob_start();
            include $viewFile;
            $content = ob_get_clean();
            
            $layoutFile = APP_PATH . 'views/layouts/' . $this->layout . '.php';
            if (file_exists($layoutFile)) {
                $body = $content;
                include $layoutFile;
            } else {
                echo $content;
            }
        }
    }
    
    /**
     * Redirect to another route
     */
    protected function redirect($path) {
        Router::redirect($path);
    }
    
    /**
     * Generate URL
     */
    protected function url($action, $params = []) {
        return Router::url($action, $params);
    }
    
    /**
     * Check if POST request
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Check if AJAX request
     */
    protected function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
    
    /**
     * Get POST data
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Get GET data
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    /**
     * Sanitize input
     */
    protected function sanitize($input, $type = 'string') {
        switch ($type) {
            case 'int':
                return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return (float) filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT);
            case 'email':
                return filter_var($input, FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var($input, FILTER_SANITIZE_URL);
            case 'string':
            default:
                return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Validate email
     */
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    public function model($model)
    {
        require_once '../app/models/' . $model . '.php';
        return new $model;
    }

    public function view($view, $data = [])
    {
        require_once '../app/views/' . $view . '.php';
    }
}

?>
