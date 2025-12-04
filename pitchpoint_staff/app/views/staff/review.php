<?php
// app/views/staff/review.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Review Project · PitchPoint Staff</title>
    <link rel="stylesheet" href="/pitchPoint/pitchpoint_staff/public/css/style.css">
<link rel="stylesheet" href="/pitchPoint/pitchpoint_staff/public/css/staff.css">

</head>
<body>

<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="staff-review-page">
    <div class="review-card">
        <h1>Review Project</h1>

        <?php if (!empty($error)): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <section class="project-details">
            <h2><?= htmlspecialchars($project['title']) ?></h2>
            <p><strong>Owner:</strong> <?= htmlspecialchars($project['owner_name']) ?></p>
            <p><strong>Company:</strong> <?= htmlspecialchars($project['company_name'] ?? '—') ?></p>
            <p><strong>Stage:</strong> <?= htmlspecialchars($project['stage']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($project['status']) ?></p>
            <p><strong>Budget:</strong> <?= is_numeric($project['budget']) ? '$' . number_format((float)$project['budget'], 0) : '—' ?></p>
            <p><strong>Created:</strong> <?= htmlspecialchars(date('Y-m-d', strtotime($project['created_at']))) ?></p>

            <h3>Summary</h3>
            <p><?= nl2br(htmlspecialchars($project['summary'] ?? 'No summary provided.')) ?></p>
        </section>

        <section class="review-form-section">
            <h3>Decision</h3>
            <form method="post" class="review-form">
                <div class="decision-options">
                    <label>
                        <input type="radio" name="decision" value="approve" <?= isset($_POST['decision']) && $_POST['decision'] === 'approve' ? 'checked' : '' ?>>
                        Approve (publish project)
                    </label>
                    <label>
                        <input type="radio" name="decision" value="reject" <?= isset($_POST['decision']) && $_POST['decision'] === 'reject' ? 'checked' : '' ?>>
                        Reject
                    </label>
                </div>

                <label class="comment-label">
                    Comment / Reason for rejection
                    <textarea name="comment" rows="4" placeholder="Explain why the project is rejected or add notes for the entrepreneur."><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
                </label>

                <div class="review-actions">
                    <button type="submit">Submit decision</button>
                    <a href="/pitchPoint/pitchpoint_staff/public/staff.php">Back to dashboard</a>
                </div>
            </form>
        </section>
    </div>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>
