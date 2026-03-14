<?php include '../App/Views/layouts/header.php'; ?>
<style>
/* Fix for long names */
.table td {
    white-space: normal;
    word-wrap: break-word;
    max-width: 200px;
}

/* Optional: limit name column width */
.table td:first-child {
    max-width: 250px;
    min-width: 150px;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Candidate Management <legend>Administrator</legend></h2>
            <div>
                <a href="/vote/admin/user-votes" class="btn btn-info">
                    <i class="bi bi-people"></i> View Who Voted
                </a>
                <a href="/vote/admin/manage-users" class="btn btn-primary ms-2">
                    <i class="bi bi-people"></i> Manage Users
                </a>
                  <a href="/vote/admin/results" class="btn btn-success ms-2">
                    <i class="bi bi-people"></i> Results
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

<!-- Add Candidate Form -->
<div class="row mt-4">
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5>Add New Candidate</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/vote/admin/add-candidate">
                    <div class="mb-3">
                        <label for="name" class="form-label">Candidate Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="position_id" class="form-label">Position</label>
                        <select class="form-control" id="position_id" name="position_id" required>
                            <option value="">Select Position</option>
                            <?php foreach($data['positions'] as $position): ?>
                                <option value="<?php echo $position['id']; ?>">
                                    <?php echo $position['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Add Candidate</button>
                </form>
            </div>
        </div>
    </div>

    
    <!-- Candidates List by Position -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5>Candidates by Position</h5>
            </div>
            <div class="card-body p-0">
                <div class="accordion" id="candidateAccordion">
                    <?php 
                    // Group candidates by position
                    $grouped_candidates = [];
                    foreach($data['candidates'] as $candidate) {
                        $grouped_candidates[$candidate['position_name']][] = $candidate;
                    }
                    
                    $position_count = 0;
                    foreach($grouped_candidates as $position_name => $candidates): 
                        $position_count++;
                    ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $position_count; ?>">
                             <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $position_count; ?>">
                                    <strong><?php echo $position_name; ?></strong>
                                    <span class="badge bg-primary ms-2"><?php echo count($candidates); ?> candidates</span>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $position_count; ?>" class="accordion-collapse collapse" data-bs-parent="#candidateAccordion">
                                <div class="accordion-body p-0">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 50%">Name</th>
                                                <th style="width: 10%">Votes</th>
                                                <th style="width: 15%">Actions</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($candidates as $candidate): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($candidate['name']); ?></td>
                                                <td>
                                                    <span class="badge bg-info rounded-pill">
                                                        <?php echo $candidate['vote_count'] ?? 0; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="/vote/admin/edit-candidate/<?php echo $candidate['id']; ?>" 
                                                           class="btn btn-warning">
                                                            Edit
                                                        </a>
                                                        <a href="/vote/admin/delete-candidate/<?php echo $candidate['id']; ?>?confirm=yes" 
                                                           class="btn btn-danger"
                                                           onclick="return confirm('Are you sure you want to delete <?php echo addslashes($candidate['name']); ?>?')">
                                                            Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Summary Row -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card bg-light">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h5>Total Candidates</h5>
                        <h3><?php echo count($data['candidates']); ?></h3>
                    </div>
                    <div class="col-md-4">
                        <h5>Total Positions</h5>
                        <h3><?php echo count($data['positions']); ?></h3>
                    </div>
                    <div class="col-md-4">
                        <h5>Total Votes</h5>
                        <h3><?php 
                            $totalVotes = 0;
                            foreach($data['candidates'] as $c) {
                                $totalVotes += $c['vote_count'] ?? 0;
                            }
                            echo $totalVotes;
                        ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../App/Views/layouts/footer.php'; ?>