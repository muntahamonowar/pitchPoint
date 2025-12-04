<?php
// Page title displayed in header
$TITLE = 'My Projects';

// Shared layout + authentication
require dirname(__DIR__) . '/includes/header.php';
require_login();

// Load logged-in entrepreneur’s projects
$pdo = db();
$stmt = $pdo->prepare(
  "SELECT project_id, title, status, created_at
   FROM projects
   WHERE entrepreneur_id = :eid
   ORDER BY created_at DESC"
);
$stmt->execute([':eid' => me_id()]);
$rows = $stmt->fetchAll();
?>

<!-- Page title + Add New Project button -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
  <h1 class="page-title">My Projects</h1>
  <a href="<?= h(base_url('create-project.php')) ?>" class="add-project-btn">
    <i class="fa fa-plus"></i>
  </a>
</div>

<?php if (!$rows): ?>

  <!-- Show message when user has no projects -->
  <p class="muted">You have no projects yet.</p>

<?php else: ?>

  <!-- Project list table -->
  <table class="table">
    <thead>
      <tr>
        <th>Title</th>
        <th>Status</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>

          <!-- Title links to public project page -->
          <td>
            <a href="<?= h(base_url('project.php?id=' . $r['project_id'])) ?>">
              <?= h($r['title']) ?>
            </a>
          </td>

          <!-- Published / Draft -->
          <td><?= h($r['status']) ?></td>

          <!-- Created date -->
          <td><?= h($r['created_at']) ?></td>

          <!-- Edit + Delete -->
          <td>
            <a class="btn" href="<?= h(base_url('edit-project.php?id=' . $r['project_id'])) ?>">Edit</a>
            <a class="btn" href="<?= h(base_url('delete-project.php?id=' . $r['project_id'])) ?>">Delete</a>
          </td>

        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

<?php endif; ?>

<?php
// Close the page layout
require dirname(__DIR__) . '/includes/footer.php';
?>
