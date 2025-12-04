<?php
// Title for page header
$TITLE = 'Project';

// Shared layout (nav, session, DB helpers)
require dirname(__DIR__) . '/includes/header.php';

// Validate project ID from URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  echo '<p class="muted">Invalid project.</p>';
  require dirname(__DIR__) . '/includes/footer.php';
  exit;
}

$pdo = db();

/**
 * Load project details including:
 * - core information (includes budget)
 * - category name (optional)
 * - cover image path (if any)
 */
$stmt = $pdo->prepare(
  "SELECT 
      p.*,
      c.category_name,
      (
        SELECT pf.storage_path
        FROM project_files pf
        WHERE pf.project_id = p.project_id
          AND pf.storage_path LIKE 'uploads/cover_image_%'
        ORDER BY pf.created_at DESC
        LIMIT 1
      ) AS cover_path
   FROM projects p
   LEFT JOIN categories c ON c.category_id = p.category_id
   WHERE p.project_id = :id"
);

$stmt->execute([':id' => $id]);
$p = $stmt->fetch();

// If project not found, stop
if (!$p) {
  echo '<p class="muted">Project not found.</p>';
  require dirname(__DIR__) . '/includes/footer.php';
  exit;
}

/**
 * Load related files
 */
$filesStmt = $pdo->prepare(
  "SELECT file_id, file_name, storage_path, mime_type
   FROM project_files
   WHERE project_id = :pid
   ORDER BY created_at DESC"
);
$filesStmt->execute([':pid' => $id]);
$files = $filesStmt->fetchAll();

/**
 * Separate out project proposal PDFs so we can show them
 * at the bottom as a dedicated “Project Proposal” section.
 */
$proposalFiles = [];

if ($files) {
  foreach ($files as $file) {
    if ($file['mime_type'] === 'application/pdf') {
      $proposalFiles[] = $file;
    }
  }
}
?>

<!-- Main Project Display -->
<article class="project">

  <!-- Title -->
  <h1><?= h($p['title']) ?></h1>

  <!-- Cover image thumbnail -->
  <?php if (!empty($p['cover_path'])): ?>
    <div style="margin: 16px 0;">
      <img 
        src="<?= h(base_url('../' . $p['cover_path'])) ?>" 
        alt="Project cover image"
        style="width:100%;max-width:420px;height:240px;object-fit:cover;border-radius:10px;">
    </div>
  <?php endif; ?>

  <!-- Owner-only actions -->
  <?php if (current_user() && (int) $p['entrepreneur_id'] === me_id()): ?>
    <p class="actions" style="margin:8px 0;">
      <a class="btn" href="<?= h(base_url('edit-project.php?id=' . $p['project_id'])) ?>">Edit</a>
      <a class="btn" href="<?= h(base_url('delete-project.php?id=' . $p['project_id'])) ?>">Delete</a>
    </p>
  <?php endif; ?>

  <!-- Metadata line -->
  <p class="muted">
    Stage: <?= h($p['stage']) ?> •
    Visibility: <?= h($p['visibility']) ?>
    <?php if (!empty($p['category_name'])): ?>
      • Category: <?= h($p['category_name']) ?>
    <?php endif; ?>
    <?php if ($p['budget'] !== null && $p['budget'] !== ''): ?>
      • Budget: <?= h($p['budget']) ?>
    <?php endif; ?>
  </p>

  <!-- Main content: summary / problem / solution -->
  <section>
    <h3>Summary</h3>
    <p><?= nl2br(h($p['summary'])) ?></p>
  </section>

  <section>
    <h3>Problem</h3>
    <p><?= nl2br(h($p['problem'])) ?></p>
  </section>

  <section>
    <h3>Solution</h3>
    <p><?= nl2br(h($p['solution'])) ?></p>
  </section>

  <!-- Project proposal section at the bottom -->
 
<?php if (!empty($proposalFiles)): ?>
  <section style="margin-top:24px;">
    <h3>Project Proposal</h3>
    <ul>
      <?php foreach ($proposalFiles as $file): ?>
        <li style="display:flex;align-items:center;gap:8px;">
          <a href="<?= h(base_url('../' . $file['storage_path'])) ?>" target="_blank">
            <?= h($file['file_name']) ?>
          </a>

          <?php if (current_user() && (int) $p['entrepreneur_id'] === me_id()): ?>
            <form 
              method="post" 
              action="<?= h(base_url('../actions/delete_project_file.php')) ?>" 
              style="margin:0;display:inline;"
            >
              <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
              <input type="hidden" name="file_id" value="<?= (int) $file['file_id'] ?>">

              <button 
                type="submit" 
                style="
                  background:none;
                  border:none;
                  padding:0;
                  font-size:0.85rem;
                  color:#b00020;
                  cursor:pointer;
                  text-decoration:underline;
                "
              >
                Delete
              </button>
            </form>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
<?php endif; ?>


<?php
// Close layout + HTML tags
require dirname(__DIR__) . '/includes/footer.php';
?>
