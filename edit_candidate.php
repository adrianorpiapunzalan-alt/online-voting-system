<?php include '../App/Views/layouts/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning">
                <h4>Edit Candidate</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="/vote/admin/update-candidate/<?php echo $data['candidate']['id']; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Candidate Name</label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo htmlspecialchars($data['candidate']['name']); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="position_id" class="form-label">Position</label>
                        <select class="form-control" id="position_id" name="position_id" required>
                            <option value="">Select Position</option>
                            <?php foreach($data['positions'] as $position): ?>
                                <option value="<?php echo $position['id']; ?>" 
                                    <?php echo $position['id'] == $data['candidate']['position_id'] ? 'selected' : ''; ?>>
                                    <?php echo $position['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-warning w-100">Update Candidate</button>
                        </div>
                        <div class="col-md-6">
                            <a href="/vote/admin/dashboard" class="btn btn-secondary w-100">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../App/Views/layouts/footer.php'; ?>