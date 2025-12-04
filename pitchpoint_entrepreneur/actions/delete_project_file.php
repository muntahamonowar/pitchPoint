<?php
// Strict type checking
declare(strict_types=1);

// Core app bootstrap (DB, helpers, auth)
require dirname(__DIR__) . '/includes/bootstrap.php';

// Must be logged in
require_login();

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

// CSRF check
if (!csrf_validate($_POST['csrf'] ?? '')) {
  flash_set('error', 'Invalid request.');
  redirect(base_url('dashboard.php'));
}

// Validate file ID
$fileId = isset($_POST['file_id']) ? (int) $_POST['file_id'] : 0;
if ($fileId <= 0) {
  flash_set('error', 'Invalid file.');
  redirect(base_url('dashboard.php'));
}

$pdo = db();

try {
  $pdo->beginTransaction();

  // Load file + owning project so we can check permissions
  $stmt = $pdo->prepare(
    "SELECT pf.file_id,
            pf.project_id,
            pf.storage_path,
            p.entrepreneur_id
     FROM project_files pf
     JOIN projects p ON p.project_id = pf.project_id
     WHERE pf.file_id = :fid"
  );
  $stmt->execute([':fid' => $fileId]);
  $file = $stmt->fetch();

  if (!$file) {
    $pdo->rollBack();
    flash_set('error', 'File not found.');
    redirect(base_url('dashboard.php'));
  }

  // Only the owner of the project may delete files
  if ((int) $file['entrepreneur_id'] !== me_id()) {
    $pdo->rollBack();
    http_response_code(403);
    flash_set('error', 'You are not allowed to delete this file.');
    redirect(base_url('project.php?id=' . (int) $file['project_id']));
  }

  // Delete DB record
  $del = $pdo->prepare("DELETE FROM project_files WHERE file_id = :fid");
  $del->execute([':fid' => $fileId]);

  // Delete physical file from disk
  $absPath = dirname(__DIR__) . '/' . $file['storage_path'];
  if (is_file($absPath)) {
    @unlink($absPath);
  }

  $pdo->commit();

  flash_set('success', 'File deleted.');
  redirect(base_url('project.php?id=' . (int) $file['project_id']));

} catch (Throwable $e) {
  $pdo->rollBack();
  error_log('delete_project_file failed: ' . $e->getMessage());
  flash_set('error', 'Could not delete file.');
  redirect(base_url('project.php?id=' . (int) ($file['project_id'] ?? 0)));
}
