<?php
// Strict type checking for safer code
declare(strict_types=1);

// Main setup (session, DB, helper functions, etc.)
require dirname(__DIR__) . '/includes/bootstrap.php';

// Only logged-in users can delete projects
require_login();

// Only allow deletion via POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

// CSRF check to prevent forged delete requests
if (!csrf_validate($_POST['csrf'] ?? '')) {
  flash_set('error', 'Invalid request.');
  redirect(base_url('my-projects.php'));
}

// Get the project ID to delete
$pid = (int) ($_POST['project_id'] ?? 0);

// Validate the ID before continuing
if ($pid <= 0) {
  flash_set('error', 'Invalid project.');
  redirect(base_url('my-projects.php'));
}

// Database connection
$pdo = db();

// Use a transaction to ensure all related data is removed safely
$pdo->beginTransaction();

try {
  //
  // Verify that the logged-in user actually owns this project
  //
  $own = $pdo->prepare(
    "SELECT entrepreneur_id
     FROM projects
     WHERE project_id = :id
     FOR UPDATE"
  );
  $own->execute([':id' => $pid]);

  if ((int) ($own->fetchColumn() ?: 0) !== me_id()) {
    // Prevent any user from deleting someone else’s project
    throw new RuntimeException('Not owner');
  }

  //
  // First remove any files associated with the project
  // (both physical files and rows from `project_files`)
  //
  $files = $pdo->prepare(
    "SELECT storage_path
     FROM project_files
     WHERE project_id = :pid"
  );
  $files->execute([':pid' => $pid]);

  // Delete files from disk
  foreach ($files->fetchAll() as $f) {
    $abs = dirname(__DIR__) . '/' . ($f['storage_path'] ?? '');
    if ($abs && is_file($abs)) {
      @unlink($abs);
    }
  }

  // Remove file records in the database
  $pdo->prepare(
    "DELETE FROM project_files
     WHERE project_id = :pid"
  )->execute([':pid' => $pid]);

  //
  // Finally, remove the project itself
  //
  $pdo->prepare(
    "DELETE FROM projects
     WHERE project_id = :pid
       AND entrepreneur_id = :eid"
  )->execute([
        ':pid' => $pid,
        ':eid' => me_id(),
      ]);

  // All good → commit changes
  $pdo->commit();
  flash_set('success', 'Project deleted.');
  redirect(base_url('my-projects.php'));

} catch (Throwable $e) {

  // Something failed → undo everything
  $pdo->rollBack();
  error_log($e->getMessage());

  flash_set('error', 'Could not delete project.');
  redirect(base_url('my-projects.php'));
}
