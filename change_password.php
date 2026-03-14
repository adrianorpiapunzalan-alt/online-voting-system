<?php include '../App/Views/layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4>Change Password for <?php echo htmlspecialchars($data['user']['full_name']); ?></h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/vote/admin/update-password/<?php echo $data['user']['id']; ?>" onsubmit="return validatePassword()">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                        <small class="text-muted">Minimum 6 characters</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-info w-100">Change Password</button>
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

<script>
function validatePassword() {
    var newPass = document.getElementById('new_password').value;
    var confirmPass = document.getElementById('confirm_password').value;
    
    if (newPass !== confirmPass) {
        alert('Passwords do not match!');
        return false;
    }
    
    if (newPass.length < 6) {
        alert('Password must be at least 6 characters!');
        return false;
    }
    
    return true;
}
</script>

<?php include '../App/Views/layouts/footer.php'; ?>