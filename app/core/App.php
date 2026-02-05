<?php

class App
{
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];
    
    public function __construct()
    {
        // Khởi tạo Router
        $router = new Router();
        
        // Lấy route từ URL
        $route = $router->route();
        
        // Gán controller, method và params từ router
        $this->controller = $route['controller'];
        $this->method = $route['action'];
        $this->params = $route['params'];
        
        // Kiểm tra file controller tồn tại
        $controllerPath = "../app/controllers/{$this->controller}.php";
        if (!file_exists($controllerPath)) {
            die("Controller {$this->controller} not found!");
        }
        
        require_once $controllerPath;
        
        // Tạo instance của controller
        $this->controller = new $this->controller;
        
        // Kiểm tra method tồn tại
        if (!method_exists($this->controller, $this->method)) {
            die("Method {$this->method} not found in {$route['controller']}!");
        }
        
        // Gọi controller method với params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }
}