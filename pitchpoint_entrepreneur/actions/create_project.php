<?php
// Use strict typing for better error catching
declare(strict_types=1);

// Main setup: session, database, auth helpers, etc.
require dirname(__DIR__) . '/includes/bootstrap.php';

// Only logged-in users are allowed to create projects
require_login();

// Only accept POST requests for form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

// Validate the CSRF token to prevent unauthorized form submissions
if (!csrf_validate($_POST['csrf'] ?? '')) {
  flash_set('error', 'Invalid request.');
  redirect(base_url('create-project.php'));
}

// Database connection
$pdo = db();

// Collect form input values and normalize them
$title       = trim($_POST['title'] ?? '');
$summary     = trim($_POST['summary'] ?? '');
$problem     = trim($_POST['problem'] ?? '');
$solution    = trim($_POST['solution'] ?? '');
$category_id = ($_POST['category_id'] ?? '') !== '' ? (int) $_POST['category_id'] : null;
$stage       = trim($_POST['stage'] ?? 'idea');
$visibility  = trim($_POST['visibility'] ?? 'public');
$budget      = ($_POST['budget'] ?? '') !== '' ? (float) $_POST['budget'] : null;
$action      = trim($_POST['action'] ?? 'draft');

// Title is required. If missing, show error and redirect back to the form
if ($title === '') {
  flash_set('error', 'Title is required.');
  redirect(base_url('create-project.php'));
}

// Entrepreneur ID of the current logged-in user
$eid = me_id();

// Use a transaction to ensure all inserts succeed together
$pdo->beginTransaction();

try {
  // Determine if the project will be published or saved as a draft
  $status = ($action === 'publish') ? 'published' : 'draft';

  // Insert project data into the database
  $sql = "INSERT INTO projects
          (entrepreneur_id, title, summary, problem, solution, category_id, budget, stage, status, visibility, created_at, updated_at)
          VALUES (:eid,:title,:summary,:problem,:solution,:category_id,:budget,:stage,:status,:visibility,NOW(),NOW())";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':eid'        => $eid,
    ':title'      => $title,
    ':summary'    => $summary,
    ':problem'    => $problem,
    ':solution'   => $solution,
    ':category_id'=> $category_id,
    ':budget'     => $budget,
    ':stage'      => $stage,
    ':status'     => $status,
    ':visibility' => $visibility,
  ]);

  // Get the ID of the newly created project
  $project_id = (int) $pdo->lastInsertId();

  // Folder where all uploaded files are stored
  $storage = dirname(__DIR__) . '/uploads';
  if (!is_dir($storage)) {
    mkdir($storage, 0777, true);
  }

  // Process file uploads (cover image, proposal PDF, video)
  foreach (['cover_image', 'project_proposal'/*, 'project_video'*/] as $field) {
    if (!empty($_FILES[$field]['name']) &&
        ($_FILES[$field]['error'] === UPLOAD_ERR_OK)) {

      $name = basename($_FILES[$field]['name']);
      $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      $new  = $field . '_' . uniqid('', true) . '.' . $ext;
      $dest = $storage . '/' . $new;

      // Move uploaded file to permanent storage
      if (move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
        $relPath = 'uploads/' . $new;

        // Record file information in database
        $ins = $pdo->prepare(
          "INSERT INTO project_files
           (project_id, file_name, storage_path, mime_type, file_size_bytes, uploaded_by, created_at)
           VALUES (:pid,:fname,:spath,:mime,:size,:uid,NOW())"
        );
        $ins->execute([
          ':pid'  => $project_id,
          ':fname'=> $name,
          ':spath'=> $relPath,
          ':mime' => $_FILES[$field]['type'] ?? null,
          ':size' => (int) ($_FILES[$field]['size'] ?? 0),
          ':uid'  => current_user()['user_id'] ?? 0,
        ]);
      }
    }
  }

  // Everything successful → save changes permanently
  $pdo->commit();
  flash_set('success', 'Project created successfully.');
  redirect(base_url('my-projects.php'));

} catch (Throwable $e) {

  // Something failed → undo changes
  $pdo->rollBack();
  error_log('create_project failed: ' . $e->getMessage());

  flash_set('error', 'Could not create project.');
  redirect(base_url('create-project.php'));
}
