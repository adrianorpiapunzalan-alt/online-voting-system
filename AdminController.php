<?php
require_once "../App/Core/Controller.php";

class AdminController extends Controller {
    private $candidateModel;
    private $voteModel;
    private $userModel;

    public function __construct() {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // SECURITY: Add headers to prevent back button access
        header("Cache-Control: no-cache, no-store, must-revalidate, private");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        // SECURITY: Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login first';
            $this->redirect('auth/login');
            exit();
        }
        
        // SECURITY: Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = 'Access denied. Admin only.';
            $this->redirect('vote');
            exit();
        }
        
        // SECURITY: Regenerate session ID periodically
        if (!isset($_SESSION['CREATED'])) {
            session_regenerate_id(true);
            $_SESSION['CREATED'] = time();
        } elseif (time() - $_SESSION['CREATED'] > 1800) {
            // Regenerate session ID every 30 minutes
            session_regenerate_id(true);
            $_SESSION['CREATED'] = time();
        }
        
        // SECURITY: Check session timeout (30 minutes)
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
            // Last request was more than 30 minutes ago
            session_unset();
            session_destroy();
            $_SESSION['error'] = 'Session expired. Please login again.';
            $this->redirect('auth/login');
            exit();
        }
        
        // Update last activity time
        $_SESSION['LAST_ACTIVITY'] = time();
        
        // Load models
        $this->candidateModel = $this->model('Candidate');
        $this->voteModel = $this->model('Vote');
        $this->userModel = $this->model('User');
    }

    // Dashboard - Show all candidates
    public function dashboard() {
        // SECURITY: Verify admin again
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('auth/login');
            return;
        }
        
        $candidates = $this->candidateModel->getAllGrouped();
        $positions = $this->candidateModel->getAllPositions();
        
        $this->view('admin/dashboard', [
            'candidates' => $candidates,
            'positions' => $positions
        ]);
    }

    // CREATE - Add new candidate
    public function addCandidate() {
        // SECURITY: Verify admin again
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('auth/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate input
            $name = trim($_POST['name'] ?? '');
            $position_id = trim($_POST['position_id'] ?? '');
            
            if (empty($name) || empty($position_id)) {
                $_SESSION['error'] = 'All fields are required';
                $this->redirect('admin/dashboard');
                return;
            }
            
            // ===== NEW CODE: Check for duplicate =====
            if ($this->candidateModel->candidateExists($name, $position_id)) {
                $_SESSION['error'] = 'This candidate name already exists for this position!';
                $this->redirect('admin/dashboard');
                return;
            }
            // ===== END OF NEW CODE =====
            
            $data = [
                'name' => $name,
                'position_id' => $position_id
            ];
            
            if ($this->candidateModel->create($data)) {
                $_SESSION['success'] = 'Candidate added successfully';
            } else {
                $_SESSION['error'] = 'Failed to add candidate';
            }
        }
        $this->redirect('admin/dashboard');
    }

    // UPDATE - Show edit form
    public function editCandidate($id) {
        // SECURITY: Verify admin again
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('auth/login');
            return;
        }
        
        // Validate ID
        if (!is_numeric($id)) {
            $_SESSION['error'] = 'Invalid candidate ID';
            $this->redirect('admin/dashboard');
            return;
        }
        
        $candidate = $this->candidateModel->findById($id);
        $positions = $this->candidateModel->getAllPositions();
        
        if (!$candidate) {
            $_SESSION['error'] = 'Candidate not found';
            $this->redirect('admin/dashboard');
            return;
        }
        
        $this->view('admin/edit_candidate', [
            'candidate' => $candidate,
            'positions' => $positions
        ]);
    }

    // UPDATE - Process edit form
    public function updateCandidate($id) {
        // SECURITY: Verify admin again
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('auth/login');
            return;
        }
        
        // Validate ID
        if (!is_numeric($id)) {
            $_SESSION['error'] = 'Invalid candidate ID';
            $this->redirect('admin/dashboard');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate input
            $name = trim($_POST['name'] ?? '');
            $position_id = trim($_POST['position_id'] ?? '');
            
            if (empty($name) || empty($position_id)) {
                $_SESSION['error'] = 'All fields are required';
                $this->redirect('admin/edit-candidate/' . $id);
                return;
            }
            
            // ===== NEW CODE: Check for duplicate when updating =====
            if ($this->candidateModel->candidateExists($name, $position_id, $id)) {
                $_SESSION['error'] = 'This candidate name already exists for this position!';
                $this->redirect('admin/edit-candidate/' . $id);
                return;
            }
            // ===== END OF NEW CODE =====
            
            $data = [
                'name' => $name,
                'position_id' => $position_id
            ];
            
            if ($this->candidateModel->update($id, $data)) {
                $_SESSION['success'] = 'Candidate updated successfully';
            } else {
                $_SESSION['error'] = 'Failed to update candidate';
            }
        }
        $this->redirect('admin/dashboard');
    }

    // DELETE - Delete candidate
    public function deleteCandidate($id) {
        // SECURITY: Verify admin again
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('auth/login');
            return;
        }
        
        // Validate ID
        if (!is_numeric($id)) {
            $_SESSION['error'] = 'Invalid candidate ID';
            $this->redirect('admin/dashboard');
            return;
        }
        
        // SECURITY: Add CSRF check (optional but good)
        $confirm = $_GET['confirm'] ?? '';
        
        if ($confirm !== 'yes') {
            $_SESSION['error'] = 'Please confirm deletion';
            $this->redirect('admin/dashboard');
            return;
        }
        
        if ($this->candidateModel->delete($id)) {
            $_SESSION['success'] = 'Candidate deleted successfully';
        } else {
            $_SESSION['error'] = 'Failed to delete candidate';
        }
        $this->redirect('admin/dashboard');
    }

    // View all users (admin only)
    public function users() {
        // SECURITY: Verify admin again
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('auth/login');
            return;
        }
        
        $users = $this->userModel->getAllUsers();
        $this->view('admin/users', ['users' => $users]);
    }

    // View results (admin only)
// View results (admin only)
public function results() {
    // SECURITY: Verify admin again
    if ($_SESSION['role'] !== 'admin') {
        $this->redirect('auth/login');
        return;
    }
    
    // Get results from Vote model
    $results = $this->voteModel->getResultsByPosition();
    $positions = $this->candidateModel->getAllPositions();
    
    // Group results by position
    $resultsByPosition = [];
    foreach ($results as $result) {
        $resultsByPosition[$result['position_id']][] = $result;
    }
    
    // Debug - remove after fixing
    // echo "<pre>"; print_r($resultsByPosition); echo "</pre>"; exit;
    
    $this->view('admin/results', [
        'results_by_position' => $resultsByPosition,
        'positions' => $positions
    ]);
}

    // Logout (security)
    public function logout() {
        session_unset();
        session_destroy();
        
        // Clear cookies if any
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        $this->redirect('auth/login');
    }
    
    // View all votes cast by users
    public function userVotes() {
        // SECURITY: Verify admin again
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('auth/login');
            return;
        }
        
        $allVotes = $this->voteModel->getAllVotesWithDetails();
        $votingSummary = $this->voteModel->getVotingSummary();
        
        // Group votes by user
        $votesByUser = [];
        foreach ($allVotes as $vote) {
            $votesByUser[$vote['voter_id']]['user_info'] = [
                'name' => $vote['voter_name'],
                'username' => $vote['voter_username']
            ];
            $votesByUser[$vote['voter_id']]['votes'][] = [
                'position' => $vote['position_name'],
                'candidate' => $vote['candidate_name'],
                'date' => $vote['vote_date']
            ];
        }
        
        $this->view('admin/user_votes', [
            'votesByUser' => $votesByUser,
            'votingSummary' => $votingSummary,
            'totalVotes' => count($allVotes)
        ]);
    }

    // View votes of specific user
    public function userVotesDetail($user_id) {
        // SECURITY: Verify admin again
        if ($_SESSION['role'] !== 'admin') {
            $this->redirect('auth/login');
            return;
        }
        
        // Validate ID
        if (!is_numeric($user_id)) {
            $_SESSION['error'] = 'Invalid user ID';
            $this->redirect('admin/user-votes');
            return;
        }
        
        $user = $this->userModel->findById($user_id);
        $userVotes = $this->voteModel->getVotesByUser($user_id);
        
        $this->view('admin/user_votes_detail', [
            'user' => $user,
            'votes' => $userVotes
        ]);
    }
    // ============ USER MANAGEMENT ============

// Show all users
public function manageUsers() {
    // SECURITY: Verify admin again
    if ($_SESSION['role'] !== 'admin') {
        $this->redirect('auth/login');
        return;
    }
    
    $users = $this->userModel->getAllUsers();
    $total_users = $this->userModel->countAllUsers();
    $total_voters = $this->userModel->countVoters();
    
    $this->view('admin/manage_users', [
        'users' => $users,
        'total_users' => $total_users,
        'total_voters' => $total_voters
    ]);
}

// Show edit user form
public function editUser($id) {
    // SECURITY: Verify admin again
    if ($_SESSION['role'] !== 'admin') {
        $this->redirect('auth/login');
        return;
    }
    
    // Validate ID
    if (!is_numeric($id)) {
        $_SESSION['error'] = 'Invalid user ID';
        $this->redirect('admin/manage-users');
        return;
    }
    
    $user = $this->userModel->findById($id);
    
    if (!$user) {
        $_SESSION['error'] = 'User not found';
        $this->redirect('admin/manage-users');
        return;
    }
    
    $this->view('admin/edit_user', ['user' => $user]);
}

// Process edit user
public function updateUser($id) {
    // SECURITY: Verify admin again
    if ($_SESSION['role'] !== 'admin') {
        $this->redirect('auth/login');
        return;
    }
    
    // Validate ID
    if (!is_numeric($id)) {
        $_SESSION['error'] = 'Invalid user ID';
        $this->redirect('admin/manage-users');
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $full_name = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $role = trim($_POST['role'] ?? 'voter');
        
        if (empty($full_name) || empty($username)) {
            $_SESSION['error'] = 'Full name and username are required';
            $this->redirect('admin/edit-user/' . $id);
            return;
        }
        
        // Check if username already exists (excluding current user)
        if ($this->userModel->usernameExists($username, $id)) {
            $_SESSION['error'] = 'Username already exists!';
            $this->redirect('admin/edit-user/' . $id);
            return;
        }
        
        $data = [
            'full_name' => $full_name,
            'username' => $username,
            'role' => $role
        ];
        
        if ($this->userModel->updateUser($id, $data)) {
            $_SESSION['success'] = 'User updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update user';
        }
    }
    $this->redirect('admin/manage-users');
}

// Show change password form
public function changeUserPassword($id) {
    // SECURITY: Verify admin again
    if ($_SESSION['role'] !== 'admin') {
        $this->redirect('auth/login');
        return;
    }
    
    // Validate ID
    if (!is_numeric($id)) {
        $_SESSION['error'] = 'Invalid user ID';
        $this->redirect('admin/manage-users');
        return;
    }
    
    $user = $this->userModel->findById($id);
    
    if (!$user) {
        $_SESSION['error'] = 'User not found';
        $this->redirect('admin/manage-users');
        return;
    }
    
    $this->view('admin/change_password', ['user' => $user]);
}

// Process change password
public function updateUserPassword($id) {
    // SECURITY: Verify admin again
    if ($_SESSION['role'] !== 'admin') {
        $this->redirect('auth/login');
        return;
    }
    
    // Validate ID
    if (!is_numeric($id)) {
        $_SESSION['error'] = 'Invalid user ID';
        $this->redirect('admin/manage-users');
        return;
    }
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_password = trim($_POST['new_password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        
        if (empty($new_password) || empty($confirm_password)) {
            $_SESSION['error'] = 'All fields are required';
            $this->redirect('admin/change-password/' . $id);
            return;
        }
        
        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = 'Passwords do not match';
            $this->redirect('admin/change-password/' . $id);
            return;
        }
        
        if (strlen($new_password) < 6) {
            $_SESSION['error'] = 'Password must be at least 6 characters';
            $this->redirect('admin/change-password/' . $id);
            return;
        }
        
        if ($this->userModel->updatePassword($id, $new_password)) {
            $_SESSION['success'] = 'Password changed successfully';
        } else {
            $_SESSION['error'] = 'Failed to change password';
        }
    }
    $this->redirect('admin/manage-users');
}

// Delete user
public function deleteUser($id) {
    // SECURITY: Verify admin again
    if ($_SESSION['role'] !== 'admin') {
        $this->redirect('auth/login');
        return;
    }
    
    // Validate ID
    if (!is_numeric($id)) {
        $_SESSION['error'] = 'Invalid user ID';
        $this->redirect('admin/manage-users');
        return;
    }
    
    // Prevent deleting yourself
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = 'You cannot delete your own account';
        $this->redirect('admin/manage-users');
        return;
    }
    
    if ($this->userModel->deleteUser($id)) {
        $_SESSION['success'] = 'User deleted successfully';
    } else {
        $_SESSION['error'] = 'Failed to delete user';
    }
    $this->redirect('admin/manage-users');
}

}
?>