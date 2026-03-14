<?php include '../App/Views/layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h4>Edit User</h4>
            </div>
            <div class="card-body">
                
                <!-- Error Message Display -->
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <!-- Success Message Display (if you want to add) -->
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="/vote/admin/update-user/<?php echo $data['user']['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <input type="text" class="form-control" value="<?php echo $data['user']['id']; ?>" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($data['user']['full_name']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($data['user']['username']); ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-warning w-100">Update User</button>
                        </div>
                        <div class="col-md-6">
                            <a href="/vote/admin/manage-users" class="btn btn-secondary w-100">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../App/Views/layouts/footer.php'; ?>