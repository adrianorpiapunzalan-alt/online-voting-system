<?php
require_once "../App/Core/Controller.php";

class HomeController extends Controller {
    public function index() {
        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['role'] == 'admin') {
                $this->redirect('admin/dashboard');
            } else {
                $this->redirect('vote');
            }
        } else {
            $this->redirect('auth/login');
        }
    }
}
?>