<?php
require_once "Controller.php";

class BaseController extends Controller {
    
    public function __construct() {
        parent::__construct();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check session timeout (30 minutes)
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
            // Last request was more than 30 minutes ago
            session_unset();
            session_destroy();
            $this->redirect('auth/login');
            exit();
        }
        
        // Update last activity time
        $_SESSION['LAST_ACTIVITY'] = time();
        
        // Check if user is logged in for protected pages
        $this->checkAuthentication();
    }
    
    protected function checkAuthentication() {
        // Override in child controllers
    }
}
?>