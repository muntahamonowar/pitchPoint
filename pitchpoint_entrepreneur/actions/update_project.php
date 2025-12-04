<?php
declare(strict_types=1);

// App setup: session, DB, helpers
require dirname(__DIR__) . '/includes/bootstrap.php';

// Page requires login
require_login();

// Must use POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

// CSRF token check
if (!csrf_validate($_POST['csrf'] ?? '')) {
  flash_set('error', 'Invalid request.');
  redirect(base_url('my-projects.php'));
}

$pdo = db();

// Project ID from the form
$pid = (int) ($_POST['project_id'] ?? 0);
if ($pid <= 0) {
  flash_set('error', 'Invalid project.');
  redirect(base_url('my-projects.php'));
}

// Verify current user owns this project
$own = $pdo->prepare(
  "SELECT entrepreneur_id FROM projects WHERE project_id = :id LIMIT 1"
);
$own->execute([':id' => $pid]);
if ((int) ($own->fetchColumn() ?: 0) !== me_id()) {
  flash_set('error', 'Not your project.');
  redirect(base_url('my-projects.php'));
}

// Collect and sanitize input fields
$title = trim($_POST['title'] ?? '');
$summary = trim($_POST['summary'] ?? '');
$problem = trim($_POST['problem'] ?? '');
$solution = trim($_POST['solution'] ?? '');
$category_id = ($_POST['category_id'] ?? '') !== '' ? (int) $_POST['category_id'] : null;
$stage = trim($_POST['stage'] ?? 'idea');
$visibility = trim($_POST['visibility'] ?? 'public');
$budget = ($_POST['budget'] ?? '') !== '' ? (float) $_POST['budget'] : null;

// Required fields check
if ($title === '') {
  flash_set('error', 'Title is required.');
  redirect(base_url('edit-project.php?id=' . $pid));
}

$pdo->beginTransaction();

try {
  // Update project main fields
  $upd = $pdo->prepare(
    "UPDATE projects
     SET title=:title, summary=:summary, problem=:problem, solution=:solution,
         category_id=:category_id, budget=:budget, stage=:stage,
         visibility=:visibility, updated_at=NOW()
     WHERE project_id=:id AND entrepreneur_id=:eid"
  );
  $upd->execute([
    ':title' => $title,
    ':summary' => $summary,
    ':problem' => $problem,
    ':solution' => $solution,
    ':category_id' => $category_id,
    ':budget' => $budget,
    ':stage' => $stage,
    ':visibility' => $visibility,
    ':id' => $pid,
    ':eid' => me_id(),
  ]);

  // Upload directory for files
  $storage = dirname(__DIR__) . '/uploads';
  if (!is_dir($storage)) {
    mkdir($storage, 0777, true);
  }

  // Optional new files: cover image + pitch deck
  foreach (['cover_image', 'project_proposal'/*,'project_video'*/] as $field) {
    if (
      !empty($_FILES[$field]['name']) &&
      $_FILES[$field]['error'] === UPLOAD_ERR_OK
    ) {

      $name = basename($_FILES[$field]['name']);
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      $new = $field . '_' . uniqid('', true) . '.' . $ext;
      $dest = $storage . '/' . $new;

      if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
        continue;
      }

      // Insert record for uploaded file
      $rel = 'uploads/' . $new;
      $ins = $pdo->prepare(
        "INSERT INTO project_files
         (project_id, file_name, storage_path, mime_type, file_size_bytes, uploaded_by, created_at)
         VALUES (:pid,:fname,:spath,:mime,:size,:uid,NOW())"
      );
      $ins->execute([
        ':pid' => $pid,
        ':fname' => $name,
        ':spath' => $rel,
        ':mime' => $_FILES[$field]['type'] ?? null,
        ':size' => (int) ($_FILES[$field]['size'] ?? 0),
        ':uid' => current_user()['user_id'] ?? 0,
      ]);
    }
  }

  // Save all changes
  $pdo->commit();
  flash_set('success', 'Project updated.');
  redirect(base_url('project.php?id=' . $pid));

} catch (Throwable $e) {

  // Roll back if update fails
  $pdo->rollBack();
  error_log($e->getMessage());

  flash_set('error', 'Could not update project.');
  redirect(base_url('edit-project.php?id=' . $pid));
}
