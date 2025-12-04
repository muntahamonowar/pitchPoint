<?php
// Page title for header display
$TITLE = 'Dashboard';

// Load layout (nav + session + helpers)
require dirname(__DIR__) . '/includes/header.php';

// Database connection
$pdo = db();

/**
 * Load the latest 12 visible projects.
 * Includes:
 * - project details (now including budget)
 * - latest cover image if available
 */
$sql = "SELECT
          p.project_id,
          p.entrepreneur_id,
          p.title,
          p.summary,
          p.stage,
          p.visibility,
          p.budget,
          p.created_at,
          (
            SELECT pf.storage_path
            FROM project_files pf
            WHERE pf.project_id = p.project_id
              AND pf.storage_path LIKE 'uploads/cover_image_%'
            ORDER BY pf.created_at DESC
            LIMIT 1
          ) AS cover_path
        FROM projects p
        WHERE p.status <> 'deleted' OR p.status IS NULL
        ORDER BY p.created_at DESC
        LIMIT 12";
$projects = $pdo->query($sql)->fetchAll();

// Current user's entrepreneur ID (0 if guest)
$mine = current_user() ? me_id() : 0;
?>

<h1 class="page-title">Recent Projects</h1>

<?php if (!$projects): ?>
  <!-- Show message when list is empty -->
  <p class="muted">
    No projects yet.
    <a class="btn" href="<?= h(base_url('create-project.php')) ?>">Create one</a>.
  </p>

<?php else: ?>
  <!-- Project listing -->
  <ul class="cards">
    <?php foreach ($projects as $p): ?>
      <li class="card">

        <!-- Cover image preview (if uploaded) -->
        <?php if (!empty($p['cover_path'])): ?>
          <a href="<?= h(base_url('project.php?id=' . $p['project_id'])) ?>">
            <img src="<?= h(base_url('../' . $p['cover_path'])) ?>" alt="Cover"
              style="width:100%;height:160px;object-fit:cover;border-radius:10px;margin-bottom:10px;">
          </a>
        <?php endif; ?>

        <!-- Title linking to project page -->
        <h3>
          <a href="<?= h(base_url('project.php?id=' . $p['project_id'])) ?>">
            <?= h($p['title']) ?>
          </a>
        </h3>

        <!-- Summary preview -->
        <p class="card-summary"><?= h($p['summary']) ?></p>

        <!-- Extra project info (now includes budget) -->
        <div class="meta">
          <span>Stage: <?= h($p['stage']) ?></span>
          <span>Visibility: <?= h($p['visibility']) ?></span>
          <?php if ($p['budget'] !== null && $p['budget'] !== ''): ?>
            <span>Budget: <?= h($p['budget']) ?></span>
          <?php endif; ?>
          <span><?= h($p['created_at']) ?></span>
        </div>

        <!-- Show Edit + Delete only for owner -->
        <?php if ((int) $p['entrepreneur_id'] === $mine): ?>
          <div class="actions" style="margin-top:8px">
            <a class="btn" href="<?= h(base_url('edit-project.php?id=' . $p['project_id'])) ?>">Edit</a>
            <a class="btn" href="<?= h(base_url('delete-project.php?id=' . $p['project_id'])) ?>">Delete</a>
          </div>
        <?php endif; ?>

      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php
// Close page layout
require dirname(__DIR__) . '/includes/footer.php';
?>
