<?php include '../App/Views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2>Election Results</h2>
        <hr>
    </div>
</div>

<?php foreach($data['positions'] as $position): ?>
    <div class="card mt-3">
        <div class="card-header bg-primary text-white">
            <h5><?php echo $position['name']; ?></h5>
        </div>
        <div class="card-body">
            <?php 
            $results = $data['results_by_position'][$position['id']] ?? [];
            if(empty($results)): 
            ?>
                <p class="text-muted">No votes yet for this position.</p>
            <?php else: ?>
                <?php 
                $totalVotes = array_sum(array_column($results, 'vote_count'));
                $maxVotes = max(array_column($results, 'vote_count'));
                
                // Count how many have the max votes (check for tie)
                $winnerCount = 0;
                foreach($results as $result) {
                    if($result['vote_count'] == $maxVotes && $maxVotes > 0) {
                        $winnerCount++;
                    }
                }
                ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Votes</th>
                            <th>Percentage</th>
                            <th>Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($results as $result): 
                            $percentage = $totalVotes > 0 ? round(($result['vote_count'] / $totalVotes) * 100, 1) : 0;
                            $isWinner = $result['vote_count'] == $maxVotes && $maxVotes > 0;
                            $isUniqueWinner = $isWinner && $winnerCount == 1;
                        ?>
                        <tr class="<?php echo $isWinner ? 'table-success' : ''; ?>">
                            <td>
                                <strong><?php echo htmlspecialchars($result['candidate_name']); ?></strong>
                                <?php if($isUniqueWinner): ?>
                                    <span class="badge bg-success">🏆 Winner</span>
                                <?php elseif($isWinner && $winnerCount > 1): ?>
                                    <span class="badge bg-warning text-dark">🤝 Tie</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $result['vote_count']; ?></td>
                            <td><?php echo $percentage; ?>%</td>
                            <td style="width: 300px;">
                                <div class="progress">
                                    <div class="progress-bar bg-<?php echo $isWinner ? ($winnerCount > 1 ? 'warning' : 'success') : 'primary'; ?>" 
                                         style="width: <?php echo $percentage; ?>%">
                                        <?php echo $percentage; ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if($winnerCount > 1 && $maxVotes > 0): ?>
                    <div class="alert alert-warning mt-3">
                        <strong>⚠️ Tie Alert:</strong> There is a tie between <?php echo $winnerCount; ?> candidates with <?php echo $maxVotes; ?> votes each.
                    </div>
                <?php elseif($maxVotes > 0): ?>
                    <div class="alert alert-success mt-3">
                        <strong>🏆 Winner:</strong> 
                        <?php 
                        foreach($results as $result) {
                            if($result['vote_count'] == $maxVotes) {
                                echo htmlspecialchars($result['candidate_name']);
                                break;
                            }
                        }
                        ?> wins with <?php echo $maxVotes; ?> votes!
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>

<div class="row mt-4">
    <div class="col-md-12 text-center">
        <a href="/vote" class="btn btn-primary">Back to Voting</a>
    </div>
</div>

<?php include '../App/Views/layouts/footer.php'; ?>