<?php include '../App/Views/layouts/header.php'; ?>

<div class="row align-items-center min-vh-80">
    <!-- Left side - Introduction -->
    <div class="col-md-6 mb-4 mb-md-0">
        <div class="pe-md-4">
            <h1 class="display-4 fw-bold text-primary mb-3">Welcome Back!</h1>
            <p class="lead mb-4">Secure Online Voting System for your organization.</p>
            
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                    <i class="bi bi-shield-check text-primary fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-1">Secure Voting</h5>
                    <p class="text-muted mb-0">Your vote is securely recorded and counted</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center mb-3">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                    <i class="bi bi-person-check text-success fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-1">Easy to Use</h5>
                    <p class="text-muted mb-0">Simple interface for all users</p>
                </div>
            </div>
            
            <div class="d-flex align-items-center mb-4">
                <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                    <i class="bi bi-bar-chart text-info fs-4"></i>
                </div>
                <div>
                    <h5 class="mb-1">Real-time Results</h5>
                    <p class="text-muted mb-0">See results as they come in</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right side - Login Form -->
    <div class="col-md-4">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center py-3">
                <h4 class="mb-0">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </h4>
            </div>
            <div class="card-body p-4">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="/vote/auth/login">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person"></i> Username
                        </label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="bi bi-lock"></i> Password
                        </label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </form>
                
                <hr class="my-4">
                
                <p class="text-center mb-0">
                    Don't have an account? <a href="/vote/auth/register" class="text-decoration-none">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>


<div class="fixed-bottom text-center text-muted py-3 bg-white">
    <small>© 2026 Online Voting System.</small>
</div>

<?php include '../App/Views/layouts/footer.php'; ?>