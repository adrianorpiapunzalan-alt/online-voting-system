<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Router
require_once "../App/Core/Router.php";

// Get the URL parameter
$url = isset($_GET['url']) ? $_GET['url'] : '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);

// Default to home if empty
if (empty($url)) {
    $url = 'home';
}

// Create router instance
$router = new Router();

// ============================================
// HOME ROUTES
// ============================================
$router->add('home', 'HomeController', 'index', 'GET');

// ============================================
// AUTHENTICATION ROUTES
// ============================================
// Login
$router->add('auth/login', 'AuthController', 'login', 'GET');
$router->add('auth/login', 'AuthController', 'login', 'POST');

// Register (regular users)
$router->add('auth/register', 'AuthController', 'register', 'GET');
$router->add('auth/register', 'AuthController', 'register', 'POST');

// Register Admin (special admin creation)
$router->add('auth/register-admin', 'AuthController', 'registerAdmin', 'GET');
$router->add('auth/register-admin', 'AuthController', 'registerAdmin', 'POST');

// Logout
$router->add('auth/logout', 'AuthController', 'logout', 'GET');

// ============================================
// ADMIN DASHBOARD ROUTES
// ============================================
$router->add('admin/dashboard', 'AdminController', 'dashboard', 'GET');

// ============================================
// ADMIN USER MANAGEMENT (FULL CRUD)
// ============================================
// View all users
$router->add('admin/users', 'AdminController', 'users', 'GET');

// Add new user
$router->add('admin/add-user', 'AdminController', 'addUser', 'POST');

// Edit user (GET form, POST update)
$router->add('admin/edit-user/{id}', 'AdminController', 'editUser', 'GET');
$router->add('admin/edit-user/{id}', 'AdminController', 'editUser', 'POST');

// Reset user password
$router->add('admin/reset-password/{id}', 'AdminController', 'resetPassword', 'POST');

// Delete user
$router->add('admin/delete-user/{id}', 'AdminController', 'deleteUser', 'GET');

// ============================================
// ADMIN CANDIDATE MANAGEMENT (FULL CRUD)
// ============================================
// Add candidate
$router->add('admin/add-candidate', 'AdminController', 'addCandidate', 'POST');

// Edit candidate (GET form, POST update)
$router->add('admin/edit-candidate/{id}', 'AdminController', 'editCandidate', 'GET');
$router->add('admin/edit-candidate/{id}', 'AdminController', 'editCandidate', 'POST');

// Delete candidate
$router->add('admin/delete-candidate/{id}', 'AdminController', 'deleteCandidate', 'GET');

// ============================================
// ADMIN ADMIN CREATION (SPECIAL)
// ============================================
// Create admin account (with auto-generated password)
$router->add('admin/create-admin', 'AdminController', 'createAdmin', 'GET');
$router->add('admin/create-admin', 'AdminController', 'createAdmin', 'POST');

// ============================================
// VOTER ROUTES
// ============================================
// Main voting page
$router->add('vote', 'VoteController', 'index', 'GET');

// Cast vote
$router->add('vote/cast', 'VoteController', 'cast', 'POST');

// View results
$router->add('vote/results', 'VoteController', 'results', 'GET');
$router->add('vote/results/{id}', 'VoteController', 'results', 'GET');

// Voting history
$router->add('vote/history', 'VoteController', 'history', 'GET');

// Vote confirmation
$router->add('vote/confirmation/{id}', 'VoteController', 'confirmation', 'GET');

// ============================================
// VOTER DASHBOARD ROUTES
// ============================================
$router->add('voter/dashboard', 'VoterController', 'dashboard', 'GET');
$router->add('voter/candidates', 'VoterController', 'candidates', 'GET');
$router->add('voter/results', 'VoterController', 'results', 'GET');
$router->add('voter/simple-vote', 'VoterController', 'simpleVote', 'GET');
$router->add('voter/simple-results', 'VoterController', 'simpleResults', 'GET');

// ============================================
// ADDITIONAL UTILITY ROUTES
// ============================================
// About page
$router->add('about', 'HomeController', 'about', 'GET');

// Contact page
$router->add('contact', 'HomeController', 'contact', 'GET');
// Candidate CRUD Routes
$router->add('admin/dashboard', 'AdminController', 'dashboard', 'GET');
$router->add('admin/add-candidate', 'AdminController', 'addCandidate', 'POST');
$router->add('admin/edit-candidate/{id}', 'AdminController', 'editCandidate', 'GET');
$router->add('admin/update-candidate/{id}', 'AdminController', 'updateCandidate', 'POST');
$router->add('admin/delete-candidate/{id}', 'AdminController', 'deleteCandidate', 'GET');
// Admin routes
$router->add('admin/dashboard', 'AdminController', 'dashboard', 'GET');
$router->add('admin/add-candidate', 'AdminController', 'addCandidate', 'POST');
$router->add('admin/edit-candidate/{id}', 'AdminController', 'editCandidate', 'GET');
$router->add('admin/update-candidate/{id}', 'AdminController', 'updateCandidate', 'POST');
$router->add('admin/delete-candidate/{id}', 'AdminController', 'deleteCandidate', 'GET');

// Vote routes
$router->add('vote', 'VoteController', 'index', 'GET');
$router->add('vote/cast', 'VoteController', 'cast', 'POST');
$router->add('vote/results', 'VoteController', 'results', 'GET');
// Admin User Votes Routes
$router->add('admin/user-votes', 'AdminController', 'userVotes', 'GET');
$router->add('admin/user-votes-detail/{id}', 'AdminController', 'userVotesDetail', 'GET');
// User Management Routes
$router->add('admin/manage-users', 'AdminController', 'manageUsers', 'GET');
$router->add('admin/edit-user/{id}', 'AdminController', 'editUser', 'GET');
$router->add('admin/update-user/{id}', 'AdminController', 'updateUser', 'POST');
$router->add('admin/change-password/{id}', 'AdminController', 'changeUserPassword', 'GET');
$router->add('admin/update-password/{id}', 'AdminController', 'updateUserPassword', 'POST');
$router->add('admin/delete-user/{id}', 'AdminController', 'deleteUser', 'GET');
// Admin results route
$router->add('admin/results', 'AdminController', 'results', 'GET');

// ============================================
// DISPATCH THE REQUEST
// ============================================
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($url, $method);
?>