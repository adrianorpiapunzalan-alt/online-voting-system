<!DOCTYPE html>
<html lang="en">
<script>
// Prevent back/forward button navigation
history.pushState(null, null, location.href);
window.onpopstate = function () {
    history.go(1);
};

// Disable right click (optional)
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

// Detect browser back/forward buttons
window.addEventListener('load', function() {
    setTimeout(function() {
        window.addEventListener('popstate', function() {
            // Force reload the page
            location.reload();
        });
    }, 0);
});
</script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Voting System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { max-width: 1200px; }
.navbar {
    position: sticky;
    top: 0;
    z-index: 1000;
    width: 100%;
}
body { 
    padding-top: 0; 
}
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/vote/">Online Voting System</a>
            <div class="navbar-nav ms-auto">
                <?php if(isset($_SESSION['username'])): ?>
                    <span class="nav-link text-white">Welcome, <?php echo $_SESSION['full_name']; ?></span>
                    <a class="nav-link" href="/vote/auth/logout">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="/vote/auth/login">Login</a>
                    <a class="nav-link" href="/vote/auth/register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <div class="container mt-4">