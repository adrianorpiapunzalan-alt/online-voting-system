<?php include '../App/Views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>User Management </h2>
            <div>
                  <a href="/vote/admin/dashboard" class="btn btn-secondary ms-2">
                    <i class="bi bi-people"></i> ←Back to Dashboard
                </a>
            </div>
        </div>
        <hr>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <h3><?php echo $data['total_users']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Voters</h5>
                <h3><?php echo $data['total_voters']; ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">All Users</h5>
    </div>
    <div class="card-body">
        <?php if(empty($data['users'])): ?>
            <p class="text-muted">No users found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['users'] as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'info'; ?>">
                                    <?php echo $user['role']; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="/vote/admin/edit-user/<?php echo $user['id']; ?>" 
                                       class="btn btn-warning" title="Edit">
                                        Edit
                                    </a>
                                    <a href="/vote/admin/change-password/<?php echo $user['id']; ?>" 
                                       class="btn btn-info" title="Change Password">
                                        Password
                                    </a>
                                    <?php if($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="/vote/admin/delete-user/<?php echo $user['id']; ?>" 
                                           class="btn btn-danger" 
                                           onclick="return confirm('Are you sure you want to delete <?php echo addslashes($user['full_name']); ?>?')"
                                           title="Delete">
                                            Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../App/Views/layouts/footer.php'; ?>     