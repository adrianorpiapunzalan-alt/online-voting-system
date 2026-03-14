<?php
class Controller {
    
    public function __construct() {
        // Add security headers to prevent browser caching
        $this->addSecurityHeaders();
    }
    
    protected function addSecurityHeaders() {
        // Prevent browser caching
        header("Cache-Control: no-cache, no-store, must-revalidate, private");
        header("Pragma: no-cache");
        header("Expires: 0");
        header("X-Frame-Options: DENY");
        header("X-Content-Type-Options: nosniff");
        header("X-XSS-Protection: 1; mode=block");
    }
    
    protected function model($model) {
        $modelFile = "../App/Models/" . $model . ".php";
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        return null;
    }

    protected function view($view, $data = []) {
        extract($data);
        require_once "../App/Views/" . $view . ".php";
    }

    protected function redirect($url) {
        // Clear any output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Add security headers before redirect
        $this->addSecurityHeaders();
        
        header("Location: /vote/" . $url);
        exit();
    }
    
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    protected function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
    }
    
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            exit();
        }
    }
    
    protected function requireAdmin() {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            $this->redirect('vote');
            exit();
        }
    }
}
?>