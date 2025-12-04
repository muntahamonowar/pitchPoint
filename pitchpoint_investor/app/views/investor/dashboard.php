<?php
// Page title for layout
$title = "Investor Dashboard";
?>

<h1>Welcome, <?= htmlspecialchars($user['name'] ?? 'Investor') ?></h1>

<!-- Explore projects section -->
<section>
    <h2>Explore Projects</h2>

    <?php if (empty($explore)): ?>
        <p class="muted">No projects yet.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($explore as $p): ?>
                <a class="card" href="<?= BASE_URL ?>/project/show/<?= (int)$p['project_id'] ?>">
                    <?php if (!empty($p['cover_image'])): ?>
                        <div class="project-image">
                            <img
                                src="/pitchPoint/pitchpoint_entrepreneur/<?= htmlspecialchars($p['cover_image']) ?>"
                                alt="<?= htmlspecialchars($p['title']) ?>"
                                style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px 12px 0 0; margin: -20px -20px 12px -20px;"
                            >
                        </div>
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                    <p class="muted"><?= htmlspecialchars($p['category_name'] ?? '—') ?></p>
                    <p><?= htmlspecialchars($p['summary'] ?? '') ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Investments section -->
<section>
    <h2>Projects You Have Invested In</h2>

    <?php if (empty($invested)): ?>
        <p class="muted">No investments yet.</p>
    <?php else: ?>
        <div class="grid">
            <?php foreach ($invested as $p): ?>
                <a class="card" href="<?= BASE_URL ?>/project/show/<?= (int)$p['project_id'] ?>">
                    <?php if (!empty($p['cover_image'])): ?>
                        <div class="project-image">
                            <img
                                src="/pitchPoint/pitchpoint_entrepreneur/<?= htmlspecialchars($p['cover_image']) ?>"
                                alt="<?= htmlspecialchars($p['title']) ?>"
                                style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px 12px 0 0; margin: -20px -20px 12px -20px;"
                            >
                        </div>
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                    <p class="muted"><?= htmlspecialchars($p['category_name'] ?? '—') ?></p>
                    <p>
                        Invested:
                        <strong><?= number_format((float)$p['amount'], 2) ?></strong>
                        on <?= htmlspecialchars(date('Y-m-d', strtotime($p['investment_date']))) ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
