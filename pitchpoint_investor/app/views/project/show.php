<?php
// app/views/project/show.php
// You have: $proj, $inv, $isInterested
?>

<a href="<?= route('project/explore') ?>" class="btn btn-secondary">← Back</a>

<h1><?= htmlspecialchars($proj['title'], ENT_QUOTES, 'UTF-8') ?></h1>

<?php if (!empty($proj['summary'])): ?>
    <p>
        <strong>Summary:</strong><br>
        <?= nl2br(htmlspecialchars($proj['summary'], ENT_QUOTES, 'UTF-8')) ?>
    </p>
<?php endif; ?>

<?php if (!empty($proj['problem'])): ?>
    <h2>Problem</h2>
    <p><?= nl2br(htmlspecialchars($proj['problem'], ENT_QUOTES, 'UTF-8')) ?></p>
<?php endif; ?>

<?php if (!empty($proj['solution'])): ?>
    <h2>Solution</h2>
    <p><?= nl2br(htmlspecialchars($proj['solution'], ENT_QUOTES, 'UTF-8')) ?></p>
<?php endif; ?>

<p>
    <strong>Stage:</strong> <?= htmlspecialchars($proj['stage'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
    <strong>Status:</strong> <?= htmlspecialchars($proj['status'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
    <strong>Visibility:</strong> <?= htmlspecialchars($proj['visibility'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>

    <?php if (!empty($proj['budget'])): ?>
        <strong>Budget:</strong> <?= htmlspecialchars($proj['budget'], ENT_QUOTES, 'UTF-8') ?><br>
    <?php endif; ?>
</p>

<hr>

<div class="actions">
    <!-- Invest -->
    <a href="<?= route('project/invest/' . (int)$proj['project_id']) ?>" class="btn btn-primary">
        Invest
    </a>

    <!-- Interest -->
    <a href="<?= route('project/toggleInterest/' . (int)$proj['project_id']) ?>" class="btn btn-outline">
        <?= $isInterested ? 'Interested ✔' : 'Mark Interest' ?>
    </a>
</div>
