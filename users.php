<?php include '../App/Views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2>User Management</h2>
        <a href="/vote/admin/dashboard" class="btn btn-secondary mb-3">← Back to Dashboard</a>
        <hr>
    </div>
</div>

<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- Add User Modal Button -->
<button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
    <i class="bi bi-person-plus"></i> Add New User
</button>

<a href="/vote/admin/create-admin" class="btn btn-danger mb-3">
    <i class="bi bi-shield-lock"></i> Create Admin
</a>

<!-- Users Table -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5>All Users</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data['users'] as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['full_name']; ?></td>
                    <td>
                        <span class="badge bg-<?php echo $user['role'] == 'admin' ? 'danger' : 'primary'; ?>">
                            <?php echo $user['role']; ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <a href="/vote/admin/edit-user/<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                        
                        <!-- Reset Password Modal Trigger -->
                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#resetPass<?php echo $user['id']; ?>">
                            Reset Pass
                        </button>
                        
                        <?php if($user['id'] != $_SESSION['user_id']): ?>
                            <a href="/vote/admin/delete-user/<?php echo $user['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Delete this user?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                
                <!-- Reset Password Modal -->
                <div class="modal fade" id="resetPass<?php echo $user['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Reset Password for <?php echo $user['username']; ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="/vote/admin/reset-password/<?php echo $user['id']; ?>">
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="text" class="form-control" name="new_password" value="<?php echo substr(md5(uniqid()), 0, 8); ?>" required>
                                        <small class="text-muted">Copy this password to share with user</small>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Reset Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/vote/admin/add-user">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" value="password123" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" name="role">
                            <option value="voter">Voter</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../App/Views/layouts/footer.php'; ?>