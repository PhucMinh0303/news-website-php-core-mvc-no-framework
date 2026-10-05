<?php

/**
 * Base Controller Class
 * All controllers inherit from this class
 */

// core/Controller.php

class Controller
{
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
     * @var string Page title
     */
    protected $pageTitle;

    /**
     * Set page title
     */
    protected function setPageTitle($title)
    {
        $this->pageTitle = $title;
        $this->data['page_title'] = $title;
        return $this;
    }

    /**
     * Set view data
     */
    protected function setData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Set multiple view data
     */
    protected function setDataArray($data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Get view data value
     */
    protected function getData($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * Render a view file
     */
    protected function render($view, $data = [], $layout = null)
    {
        $this->view = $view;

        if (is_array($data)) {
            $this->data = array_merge($this->data, $data);
        } elseif (is_bool($data) || is_string($data)) {
            $layout = $data;
        }

        if ($layout !== null) {
            $this->layout = $layout;
        }

        return $this;
    }

    /**
     * Render a view without layout
     */
    protected function renderPartial($view)
    {
        return $this->render($view, false);
    }

    /**
     * Output the view (called after action method)
     */
    public function output()
    {
        $viewFile = APP_PATH . 'views/' . $this->view . '.php';

        if (!file_exists($viewFile)) {
            die("View not found: {$viewFile}");
        }

        // Extract data into view scope
        extract($this->data);

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
    protected function redirect($path)
    {
        $baseUrl = rtrim(BASE_URL, '/');
        header("Location: {$baseUrl}/" . ltrim($path, '/'));
        exit;
    }

    /**
     * Generate URL
     */
    protected function url($action, $params = [])
    {
        $url = BASE_URL . ltrim($action, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return $url;
    }

    /**
     * Check if POST request
     */
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if AJAX request
     */
    protected function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Get POST data
     */
    protected function post($key = null, $default = null)
    {
        if ($key === null) {
            return $_POST;
        }
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }

    /**
     * Get GET data
     */
    protected function get($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }

    /**
     * Sanitize input
     */
    protected function sanitize($input, $type = 'string')
    {
        switch ($type) {
            case 'int':
                return (int)filter_var($input, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return (float)filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT);
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
    protected function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate phone number
     */
    protected function validatePhone($phone)
    {
        return preg_match('/^[0-9]{10,15}$/', $phone);
    }

    /**
     * Load model
     */
    public function model($model)
    {
        require_once APP_PATH . 'models/' . $model . '.php';
        return new $model;
    }

    /**
     * Load view with data
     */
    protected function view($view, $data = [])
    {
        $this->view = $view;
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Get POST data from JSON request
     */
    protected function getPostData()
    {
        $content = file_get_contents('php://input');
        if (!empty($content)) {
            return json_decode($content, true) ?? $_POST;
        }
        return $_POST;
    }

    /**
     * Validate required fields
     */
    protected function validateRequired($data, $fields)
    {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "The {$field} field is required";
            }
        }
        return $errors;
    }

    /**
     * Sanitize input data recursively
     */
    protected function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Set flash message
     */
    protected function setFlash($key, $message)
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Get flash message and delete
     */
    protected function getFlash($key)
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    /**
     * Check if user is admin
     */
    protected function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    /**
     * Require admin access
     */
    protected function requireAdmin()
    {
        if (!$this->isAdmin()) {
            $this->redirect('admin/login');
            exit;
        }
    }

    /**
     * Require user login
     */
    protected function requireLogin()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('login');
            exit;
        }
    }
}
