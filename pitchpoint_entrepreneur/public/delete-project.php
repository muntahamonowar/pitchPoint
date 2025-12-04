<?php
// Page title for header
$TITLE = 'Delete Project';

// Load shared layout + authentication
require dirname(__DIR__) . '/includes/header.php';
require_login();

// Validate project ID from the URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  echo '<p class="muted">Invalid project.</p>';
  require dirname(__DIR__) . '/includes/footer.php';
  exit;
}

$pdo = db();

// Confirm the logged-in user owns this project
$check = $pdo->prepare(
  "SELECT title
   FROM projects
   WHERE project_id = :id AND entrepreneur_id = :eid"
);
$check->execute([
  ':id' => $id,
  ':eid' => me_id(),
]);
$row = $check->fetch();

// If not found or not owned, show error
if (!$row) {
  echo '<p class="muted">Project not found.</p>';
  require dirname(__DIR__) . '/includes/footer.php';
  exit;
}
?>

<h1 class="page-title">Delete Project</h1>

<p>
  Are you sure you want to delete
  “<strong><?= h($row['title']) ?></strong>”?
  This cannot be undone.
</p>

<!-- Confirm deletion form -->
<form class="form" method="post" action="<?= h(base_url('../actions/delete_project.php')) ?>">

  <!-- CSRF protection -->
  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

  <!-- Pass project ID to backend -->
  <input type="hidden" name="project_id" value="<?= (int) $id ?>">

  <div class="actions">
    <button class="btn btn-primary" type="submit">Yes, delete</button>
    <a class="btn" href="<?= h(base_url('my-projects.php')) ?>">Cancel</a>
  </div>
</form>

<?php
// Close page layout
require dirname(__DIR__) . '/includes/footer.php';
?>