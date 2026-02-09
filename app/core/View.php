<?php
/**
 * View Class - Handles view rendering
 */

class View {
    /**
     * Render a view file
     */
    public static function render($view, $data = []) {
        extract($data, EXTR_PREFIX_ALL, 'view');
        
        $file = APP_PATH . 'views/' . $view . '.php';
        
        if (!file_exists($file)) {
            die("View file not found: {$file}");
        }
        
        ob_start();
        include $file;
        return ob_get_clean();
    }
    
    /**
     * Render a layout with content
     */
    public static function renderLayout($layout, $content, $data = []) {
        extract($data, EXTR_PREFIX_ALL, 'view');
        
        $layoutFile = APP_PATH . 'views/layouts/' . $layout . '.php';
        
        if (!file_exists($layoutFile)) {
            return $content;
        }
        
        $body = $content;
        
        ob_start();
        include $layoutFile;
        return ob_get_clean();
    }
    
    /**
     * Include a partial view
     */
    public static function partial($partial, $data = []) {
        return static::render('partials/' . $partial, $data);
    }
    
    /**
     * Escape HTML output
     */
    public static function escape($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Create an asset URL with cache-busting
     */
    public static function asset(string $path) {
        $assetPath = ROOT_PATH . 'public/assets/' . ltrim($path, '/');
        $url = BASE_URL . 'public/assets/' . ltrim($path, '/');
        
        // Add cache-busting via file modification time
        if (file_exists($assetPath)) {
            $version = filemtime($assetPath);
            $url .= '?v=' . $version;
        }
        
        return $url;
    }
    
    /**
     * Create a URL for a route
     */
    public static function url($route, $params = []) {
        return Router::url($route, $params);
    }
    
    /**
     * Pass data to JavaScript
     */
    public static function js($variableName, $data) {
        $json = json_encode($data);
        return "<script>window.{$variableName} = {$json};</script>";
    }
    
    
}

?>
