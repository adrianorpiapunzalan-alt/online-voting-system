<?php
require_once "../App/Core/Controller.php";

class VoteController extends Controller {
    private $voteModel;
    private $candidateModel;

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
        
        // SECURITY: Check session timeout (30 minutes)
        if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
            session_unset();
            session_destroy();
            $_SESSION['error'] = 'Session expired. Please login again.';
            $this->redirect('auth/login');
            exit();
        }
        
        // Update last activity time
        $_SESSION['LAST_ACTIVITY'] = time();
        
        // Load models
        $this->voteModel = $this->model('Vote');
        $this->candidateModel = $this->model('Candidate');
    }

    public function index() {
        // SECURITY: Verify user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        
        $positions = $this->candidateModel->getAllPositions();
        $candidates = $this->candidateModel->getAllGrouped();
        $userVotes = $this->voteModel->getUserVotes($_SESSION['user_id']);
        
        // Group candidates by position
        $candidatesByPosition = [];
        foreach ($candidates as $candidate) {
            $candidatesByPosition[$candidate['position_id']][] = $candidate;
        }
        
        // Check which positions user already voted
        $votedPositions = [];
        foreach ($userVotes as $vote) {
            $votedPositions[] = $vote['position_id'];
        }
        
        $this->view('voter/vote', [
            'positions' => $positions,
            'candidates_by_position' => $candidatesByPosition,
            'voted_positions' => $votedPositions,
            'user_votes' => $userVotes
        ]);
    }

    public function cast() {
        // SECURITY: Verify user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $votes = $_POST['votes'] ?? [];
            $errors = [];
            $success = 0;
            
            if (empty($votes)) {
                $_SESSION['error'] = 'No votes selected';
                $this->redirect('vote');
                return;
            }
            
            foreach ($votes as $position_id => $candidate_id) {
                // Validate inputs
                if (!is_numeric($position_id) || !is_numeric($candidate_id)) {
                    $errors[] = "Invalid vote data";
                    continue;
                }
                
                // Check if already voted for this position
                if ($this->voteModel->hasVotedForPosition($_SESSION['user_id'], $position_id)) {
                    $errors[] = "You already voted for this position";
                    continue;
                }
                
                // Cast vote
                if ($this->voteModel->castVote($_SESSION['user_id'], $candidate_id, $position_id)) {
                    $success++;
                } else {
                    $errors[] = "Failed to vote for position";
                }
            }
            
            if ($success > 0) {
                $_SESSION['success'] = "Successfully voted for $success position(s)!";
            }
            
            if (!empty($errors)) {
                $_SESSION['error'] = implode('<br>', $errors);
            }
        }
        
        $this->redirect('vote');
    }

    public function results() {
        // SECURITY: Verify user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        
        $results = $this->voteModel->getResultsByPosition();
        $positions = $this->candidateModel->getAllPositions();
        
        // Group results by position
        $resultsByPosition = [];
        foreach ($results as $result) {
            $resultsByPosition[$result['position_id']][] = $result;
        }
        
        $this->view('voter/results', [
            'results_by_position' => $resultsByPosition,
            'positions' => $positions
        ]);
    }

    public function history() {
        // SECURITY: Verify user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        
        $userVotes = $this->voteModel->getUserVotes($_SESSION['user_id']);
        $this->view('voter/history', ['history' => $userVotes]);
    }

    public function confirmation($id) {
        // SECURITY: Verify user is logged in
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        
        // Validate ID
        if (!is_numeric($id)) {
            $this->redirect('vote');
            return;
        }
        
        $position = null;
        foreach ($this->candidateModel->getAllPositions() as $p) {
            if ($p['id'] == $id) {
                $position = $p;
                break;
            }
        }
        
        $this->view('voter/confirmation', ['position' => $position]);
    }

    // SECURITY: Logout method
    public function logout() {
        session_unset();
        session_destroy();
        
        // Clear cookies if any
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        $this->redirect('auth/login');
    }
}
?>