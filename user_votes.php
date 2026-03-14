<?php include '../App/Views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2>User Voting History</h2>
            
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
<!-- Summary Cards -->
<!-- Summary Cards -->
<div class="row mb-4 justify-content-center">
    <div class="col-md-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Votes</h5>
                <h3><?php echo $data['totalVotes']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Voted</h5>
                <h3><?php 
                    $votedCount = 0;
                    foreach($data['votingSummary'] as $summary) {
                        if($summary['votes']) {
                            $votedCount++;
                        }
                    }
                    echo $votedCount;
                ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Not Voted</h5>
                <h3><?php 
                    $notVotedCount = 0;
                    foreach($data['votingSummary'] as $summary) {
                        if(!$summary['votes']) {
                            $notVotedCount++;
                        }
                    }
                    echo $notVotedCount;
                ?></h3>
            </div>
        </div>
    </div>
</div>

<!-- Voting Status Table -->
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Voting Status</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Voter Name</th>
                                <th>Username</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data['votingSummary'] as $summary): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($summary['voter_name']); ?></td>
                                <td><?php echo htmlspecialchars($summary['voter_username']); ?></td>
                                <td>
                                    <?php if($summary['votes']): ?>
                                        <span class="badge bg-success">Has voted</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Has not voted</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Detailed Votes by User -->
<div class="row justify-content-center mt-4">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Voting History</h5>
            </div>
            <div class="card-body">
                <?php if(empty($data['votesByUser'])): ?>
                    <p class="text-muted">No votes have been cast yet.</p>
                <?php else: ?>
                    <div class="accordion" id="votesAccordion">
                        <?php 
                        $user_count = 0;
                        foreach($data['votesByUser'] as $user_id => $userData): 
                            $user_count++;
                        ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?php echo $user_count; ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#collapse<?php echo $user_count; ?>">
                                        <strong><?php echo htmlspecialchars($userData['user_info']['name']); ?></strong>
                                        <span class="badge bg-primary ms-2">@<?php echo $userData['user_info']['username']; ?></span>
                                        <span class="badge bg-success ms-2"><?php echo count($userData['votes']); ?> votes</span>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo $user_count; ?>" class="accordion-collapse collapse" data-bs-parent="#votesAccordion">
                                    <div class="accordion-body">
                                        <?php if(empty($userData['votes'])): ?>
                                            <p class="text-muted">This user has not voted yet.</p>
                                        <?php else: ?>
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Position</th>
                                                        <th>Voted For</th>
                                                        <th>Date & Time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($userData['votes'] as $vote): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($vote['position']); ?></td>
                                                        <td>
                                                            <span class="badge bg-info">
                                                                <?php echo htmlspecialchars($vote['candidate']); ?>
                                                            </span>
                                                        </td>
                                                        <td><?php echo date('M d, Y h:i A', strtotime($vote['date'])); ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../App/Views/layouts/footer.php'; ?>