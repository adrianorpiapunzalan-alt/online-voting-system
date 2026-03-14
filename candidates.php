<?php include '../App/Views/layouts/header.php'; ?>

<div class="row">
    <div class="col-md-12">
        <h2>Candidates</h2>
        <hr>
    </div>
</div>

<div class="row">
    <?php if(empty($data['candidates'])): ?>
        <div class="col-md-12">
            <div class="alert alert-info">No candidates available.</div>
        </div>
    <?php else: ?>
        <?php foreach($data['candidates'] as $candidate): ?>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5><?php echo htmlspecialchars($candidate['position']); ?></h5>
                </div>
                <div class="card-body text-center">
                    <h4><?php echo htmlspecialchars($candidate['name']); ?></h4>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../App/Views/layouts/footer.php'; ?>