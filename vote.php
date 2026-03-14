<?php include '../App/Views/layouts/header.php'; ?>

<style>
.position-card {
    border-left: 4px solid #007bff;
    margin-bottom: 20px;
}
.voted-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}
.candidate-option {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.2s;
}
.candidate-option:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}
.candidate-option.selected {
    background-color: #e3f2fd;
    border-color: #007bff;
}
</style>

<div class="row">
    <div class="col-md-12">
        <h2>Choose your Candidates </h2>
        <p>Welcome, <strong><?php echo $_SESSION['full_name']; ?></strong>! Please vote for each position.</p>
        <hr>
        
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
    </div>
</div>

<?php if(empty($data['candidates_by_position'])): ?>
    <div class="alert alert-warning">No candidates available for voting.</div>
<?php else: ?>
    <form method="POST" action="/vote/vote/cast" id="voteForm">
        <?php foreach($data['positions'] as $position): ?>
            <?php 
            $position_id = $position['id'];
            $candidates = $data['candidates_by_position'][$position_id] ?? [];
            $hasVoted = in_array($position_id, $data['voted_positions']);
            ?>
            
            <div class="card position-card <?php echo $hasVoted ? 'bg-light' : ''; ?>">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php echo $position['name']; ?></h5>
                    <?php if($hasVoted): ?>
                        <span class="badge bg-success">✓ Voted</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if(empty($candidates)): ?>
                        <p class="text-muted">No candidates for this position.</p>
                    <?php elseif($hasVoted): ?>
                        <?php 
                        $userVote = array_filter($data['user_votes'], function($v) use ($position_id) {
                            return $v['position_id'] == $position_id;
                        });
                        $userVote = reset($userVote);
                        ?>
                        <div class="alert alert-success">
                            You voted for: <strong><?php echo $userVote['candidate_name']; ?></strong>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach($candidates as $candidate): ?>
                                <div class="col-md-6">
                                    <div class="candidate-option" onclick="selectCandidate(<?php echo $position_id; ?>, <?php echo $candidate['id']; ?>)">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" 
                                                   name="votes[<?php echo $position_id; ?>]" 
                                                   value="<?php echo $candidate['id']; ?>" 
                                                   id="pos<?php echo $position_id; ?>_cand<?php echo $candidate['id']; ?>"
                                                   <?php echo $candidate['vote_count'] ?? ''; ?>>
                                            <label class="form-check-label" for="pos<?php echo $position_id; ?>_cand<?php echo $candidate['id']; ?>">
                                                <strong><?php echo htmlspecialchars($candidate['name']); ?></strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php 
        $allVoted = count($data['voted_positions']) >= 5;
        ?>
        
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <?php if($allVoted): ?>
                    <div class="alert alert-info">
                        <h5>You have completed all votes!</h5>
                        <a href="/vote/vote/results" class="btn btn-primary">View Results</a>
                    </div>
                <?php else: ?>
                    <button type="button" class="btn btn-success btn-lg" onclick="validateAndSubmit()">
                        Submit All Votes
                    </button>
                <?php endif; ?>
                
                
            </div>
        </div>
    </form>
<?php endif; ?>

<script>
function selectCandidate(positionId, candidateId) {
    // Remove selected class from all options in this position
    document.querySelectorAll(`.candidate-option`).forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    event.currentTarget.classList.add('selected');
    
    // Check the radio button
    document.getElementById(`pos${positionId}_cand${candidateId}`).checked = true;
}

function validateAndSubmit() {
    const positions = <?php echo json_encode(array_column($data['positions'], 'id')); ?>;
    const votedPositions = <?php echo json_encode($data['voted_positions']); ?>;
    let missing = [];
    
    positions.forEach(pos => {
        if (!votedPositions.includes(pos)) {
            const selected = document.querySelector(`input[name="votes[${pos}]"]:checked`);
            if (!selected) {
                // Find position name
                const positionCard = document.querySelector(`.position-card:has(input[name="votes[${pos}]"])`);
                const positionName = positionCard ? positionCard.querySelector('.card-header h5').textContent : `Position ${pos}`;
                missing.push(positionName);
            }
        }
    });
    
    if (missing.length > 0) {
        alert('Please vote for the following positions:\n- ' + missing.join('\n- '));
    } else {
        if (confirm('Are you sure you want to submit your votes? This action cannot be undone.')) {
            document.getElementById('voteForm').submit();
        }
    }
}
</script>

<?php include '../App/Views/layouts/footer.php'; ?>