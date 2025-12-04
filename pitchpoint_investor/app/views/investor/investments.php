<?php
// Page title for the layout
$title = "My Investments";
?>

<h1>My Investments</h1>

<?php if (empty($invested)): ?>

    <!-- Empty state when the investor has no investments -->
    <p>You haven't invested in any projects yet.</p>

<?php else: ?>

    <div class="grid">
        <?php foreach ($invested as $row): ?>
            <?php
            // Normalise project data for this card
            $title     = esc($row['title'] ?? 'Untitled project');
            $projectId = (int)($row['project_id'] ?? 0);
            ?>

            <article class="card">
                <?php if (!empty($row['cover_image'])): ?>
                    <div class="project-image">
                        <img
                            src="/pitchPoint/pitchpoint_entrepreneur/<?= htmlspecialchars($row['cover_image']) ?>"
                            alt="<?= $title ?>"
                            style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px 12px 0 0; margin: -20px -20px 12px -20px;"
                        >
                    </div>
                <?php endif; ?>

                <h2><?= $title ?></h2>

                <?php if (!empty($row['summary'])): ?>
                    <p class="muted"><?= esc($row['summary']) ?></p>
                <?php endif; ?>

                <p class="muted">
                    Category: <?= esc($row['category_name'] ?? '—') ?>
                </p>

                <p>
                    Invested:
                    <strong>
                        $<?= number_format((float)($row['amount'] ?? 0), 2) ?>
                    </strong>
                    on
                    <?php
                    if (!empty($row['investment_date'])) {
                        echo esc(date('Y-m-d', strtotime($row['investment_date'])));
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </p>

                <?php if (function_exists('route')): ?>
                    <a href="<?= route('project/show/' . $projectId) ?>" class="link">
                        View project
                    </a>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>

<?php endif; ?>
