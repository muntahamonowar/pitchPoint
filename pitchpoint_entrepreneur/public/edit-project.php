<?php
// Page title for header
$TITLE = 'Edit Project';

// Shared header + login protection
require dirname(__DIR__) . '/includes/header.php';
require_login();

// Validate project ID from the URL query
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
  echo '<p class="muted">Invalid project.</p>';
  require dirname(__DIR__) . '/includes/footer.php';
  exit;
}

$pdo = db();

// Load project only if it belongs to the logged-in entrepreneur
$stmt = $pdo->prepare(
  "SELECT *
   FROM projects
   WHERE project_id = :id
     AND entrepreneur_id = :eid
   LIMIT 1"
);
$stmt->execute([
  ':id' => $id,
  ':eid' => me_id(),
]);
$p = $stmt->fetch();

if (!$p) {
  echo '<p class="muted">Project not found.</p>';
  require dirname(__DIR__) . '/includes/footer.php';
  exit;
}

// Load list of categories for the dropdown
$cats = [];
try {
  $cats = $pdo->query(
    "SELECT category_id, category_name
     FROM categories
     ORDER BY category_name"
  )->fetchAll();
} catch (Throwable $e) {
  // If categories fail to load, list stays empty
}
?>

<h1 class="page-title">Edit Project</h1>

<!-- Form to update project -->
<form action="<?= h(base_url('../actions/update_project.php')) ?>" method="post" enctype="multipart/form-data"
  class="form">

  <!-- Security + project reference -->
  <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
  <input type="hidden" name="project_id" value="<?= (int) $p['project_id'] ?>">

  <div class="grid">

    <!-- Title -->
    <label>Title
      <input name="title" required maxlength="180" value="<?= h($p['title']) ?>">
    </label>

    <!-- Short summary -->
    <label>Summary
      <textarea name="summary" rows="2" maxlength="500">
        <?= h($p['summary']) ?>
      </textarea>
    </label>

    <!-- What problem the project solves -->
    <label>Problem
      <textarea name="problem" rows="4">
        <?= h($p['problem']) ?>
      </textarea>
    </label>

    <!-- Solution details -->
    <label>Solution
      <textarea name="solution" rows="4">
        <?= h($p['solution']) ?>
      </textarea>
    </label>

    <!-- Project category selector -->
    <label>Category
      <select name="category_id">
        <option value="">— Select —</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= (int) $c['category_id'] ?>" <?= ($p['category_id'] == $c['category_id'] ? 'selected' : '') ?>>
            <?= h($c['category_name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <!-- Development stage -->
    <label>Stage
      <select name="stage">
        <?php foreach (['idea', 'mvp', 'beta', 'launched'] as $s): ?>
          <option value="<?= $s ?>" <?= ($p['stage'] === $s ? 'selected' : '') ?>>
            <?= strtoupper($s) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <!-- Visibility setting -->
    <label>Visibility
      <select name="visibility">
        <?php foreach (['public', 'private'] as $v): ?>
          <option value="<?= $v ?>" <?= ($p['visibility'] === $v ? 'selected' : '') ?>>
            <?= ucfirst($v) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </label>

    <!-- Budget field -->
    <label>Budget (USD)
      <input name="budget" type="number" step="0.01" min="0" value="<?= h((string) ($p['budget'] ?? '')) ?>">
    </label>

    <!-- Optional file uploads -->
    <label>Replace Cover Image
      <input name="cover_image" type="file" accept="image/*">
    </label>

    <label>Project Proposal (PDF)
      <input name="project_proposal" type="file" accept="application/pdf">
    </label>
        <!-- Optional demo video for the project -->
    <!-- <label>Project Video (optional)
      <input name="project_video"
             type="file"
             accept="video/*">
    </label> -->


  </div>


  <!-- Form buttons -->
  <div class="actions">
    <button class="btn btn-primary" type="submit">Save Changes</button>
    <a class="btn" href="<?= h(base_url('my-projects.php')) ?>">Cancel</a>
  </div>

</form>

<?php
// End page layout
require dirname(__DIR__) . '/includes/footer.php';
?>