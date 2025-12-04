<?php
// Strict type checking for safer code
declare(strict_types=1);

// Load core app setup (session, DB, helpers)
require dirname(__DIR__) . '/includes/bootstrap.php';

// Must be logged in to update profile
require_login();

// Only allow updates through POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

// Validate CSRF token to ensure the request is legitimate
if (!csrf_validate($_POST['csrf'] ?? '')) {
  flash_set('error', 'Invalid request.');
  redirect(base_url('profile.php'));
}

$user = current_user();
if (!$user) {
  flash_set('error', 'Please log in.');
  header('Location: /pitchPoint/auth/login.php');
  exit;
}

$pdo = db();

$userId = (int) $user['user_id'];
$eid    = me_id();

// Collect and sanitize input fields
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$company = trim($_POST['company_name'] ?? '');
$website = trim($_POST['website'] ?? '');
$loc     = trim($_POST['location'] ?? '');

// Basic validation: name and email must not be empty
if ($name === '' || $email === '') {
  flash_set('error', 'Name and email are required.');
  redirect(base_url('profile.php'));
}

try {
  // Use a transaction for consistent DB updates
  $pdo->beginTransaction();

  // Update user fields in the users table
  $uStmt = $pdo->prepare(
    "UPDATE users
     SET name = :name,
         email = :email
     WHERE user_id = :uid
     LIMIT 1"
  );
  $uStmt->execute([
    ':name' => $name,
    ':email' => $email,
    ':uid' => $userId,
  ]);

  // Update entrepreneur details (company, website, location)
  $eStmt = $pdo->prepare(
    "UPDATE entrepreneurs
     SET company_name = :company,
         website = :website,
         location = :loc
     WHERE entrepreneur_id = :eid
     LIMIT 1"
  );
  $eStmt->execute([
    ':company' => $company !== '' ? $company : null,
    ':website' => $website !== '' ? $website : null,
    ':loc'     => $loc !== '' ? $loc : null,
    ':eid'     => $eid,
  ]);

  // Save both updates
  $pdo->commit();

} catch (Throwable $e) {
  // Undo changes if something goes wrong
  $pdo->rollBack();
  error_log('update_profile failed: ' . $e->getMessage());

  flash_set('error', 'Could not update profile.');
  redirect(base_url('profile.php'));
}

// Avatar upload (outside transaction)
// This part handles storing a new profile photo if provided
if (!empty($_FILES['profile_photo']['name']) &&
    $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {

  $uploadDir = dirname(__DIR__) . '/uploads';

  // Create upload directory if missing
  if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $origName = basename($_FILES['profile_photo']['name']);
  $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
  $allowed = ['jpg', 'jpeg', 'png', 'gif'];

  if (in_array($ext, $allowed, true)) {
    // Remove older avatar for this user if exists
    foreach (glob($uploadDir . '/avatar_' . $userId . '.*') as $old) {
      @unlink($old);
    }

    // Store new avatar with a consistent filename
    $newName = 'avatar_' . $userId . '.' . $ext;
    $dest = $uploadDir . '/' . $newName;

    // Move uploaded file to storage
    if (!move_uploaded_file($_FILES['profile_photo']['tmp_name'], $dest)) {
      flash_set('error', 'Profile updated, but could not save photo.');
      redirect(base_url('profile.php'));
    }

  } else {
    flash_set('error', 'Profile updated, but photo file type is not supported.');
    redirect(base_url('profile.php'));
  }
}

// Keep session values updated so UI reflects new name/email immediately
$_SESSION['user']['name']  = $name;
$_SESSION['user']['email'] = $email;

// Final success message
flash_set('success', 'Profile updated.');
redirect(base_url('profile.php'));
