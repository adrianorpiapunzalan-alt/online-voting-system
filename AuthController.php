<?php
require_once "../App/Core/Controller.php";

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

 public function login() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $user = $this->userModel->findByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['LAST_ACTIVITY'] = time();
            $_SESSION['CREATED'] = time();
            
            if ($user['role'] == 'admin') {
                $this->redirect('admin/dashboard');
            } else {
                $this->redirect('vote');
            }
            return;
        } else {
            $this->view('auth/login', ['error' => 'Invalid username or password']);
        }
    } else {
        $this->view('auth/login');
    }
}

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Check if username already exists
            $existingUser = $this->userModel->findByUsername($_POST['username']);
            
            if ($existingUser) {
                $this->view('auth/register', ['error' => 'Username already exists! Please choose another.']);
                return;
            }
            
            $data = [
                'username' => $_POST['username'],
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'full_name' => $_POST['full_name'],
                'role' => 'voter'
            ];
            
            if ($this->userModel->create($data)) {
                $this->redirect('auth/login');
            } else {
                $this->view('auth/register', ['error' => 'Registration failed']);
            }
        } else {
            $this->view('auth/register');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('auth/login');
    }

    public function registerAdmin() {
    // Only allow if no admin exists yet or if current user is admin
    $adminCount = $this->userModel->countAdmins();
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Check if username already exists
        $existingUser = $this->userModel->findByUsername($_POST['username']);
        
        if ($existingUser) {
            $this->view('auth/register_admin', ['error' => 'Username already exists! Please choose another.']);
            return;
        }
        
        $data = [
            'username' => $_POST['username'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'full_name' => $_POST['full_name'],
            'role' => 'admin'  // Force admin role
        ];
        
        if ($this->userModel->create($data)) {
            $_SESSION['success'] = 'Admin account created successfully!';
            $this->redirect('auth/login');
        } else {
            $this->view('auth/register_admin', ['error' => 'Registration failed']);
        }
    } else {
        $this->view('auth/register_admin');
    }
}
}
?>