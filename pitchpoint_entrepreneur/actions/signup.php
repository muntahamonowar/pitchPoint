<?php
// Strict typing for better error checking
declare(strict_types=1);

// Load app setup (DB connection, session, helpers, etc.)
require dirname(__DIR__) . '/includes/bootstrap.php';

// Only allow form submission via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Method Not Allowed');
}

// Validate CSRF token to block fake form submissions
if (!csrf_validate($_POST['csrf'] ?? '')) {
  flash_set('error', 'Invalid request.');
  redirect(base_url('signup.php'));
}

// Collect and sanitize input fields
$name    = trim($_POST['name'] ?? '');
$email   = strtolower(trim($_POST['email'] ?? ''));
$pass    = $_POST['password'] ?? '';
$company = trim($_POST['company_name'] ?? '');

// Basic validation: required fields and minimum password length
if ($name === '' || $email === '' || strlen($pass) < 6) {
  flash_set('error', 'Please fill all required fields (password ≥ 6 chars).');
  redirect(base_url('signup.php'));
}

// Check if email already exists to prevent duplicate accounts
$pdo = db();
$exists = $pdo->prepare('SELECT 1 FROM users WHERE email = :email LIMIT 1');
$exists->execute([':email' => $email]);

if ($exists->fetchColumn()) {
  flash_set('error', 'Email already in use.');
  header('Location: /pitchPoint/auth/login.php');
  exit;
}

// Use a transaction to ensure both user and entrepreneur rows are inserted safely
$pdo->beginTransaction();

try {
  // Insert new user into the `users` table
  $insUser = $pdo->prepare("
    INSERT INTO users (name, email, password_hash, role, is_active, created_at, updated_at)
    VALUES (:n, :e, :ph, 'entrepreneur', 1, NOW(), NOW())
  ");
  $insUser->execute([
    ':n'  => $name,
    ':e'  => $email,
    ':ph' => password_hash($pass, PASSWORD_DEFAULT),
  ]);

  // Get new user's ID
  $uid = (int) $pdo->lastInsertId();

  // Insert entrepreneur record linked with user
  $insEnt = $pdo->prepare("
    INSERT INTO entrepreneurs (user_id, company_name, created_at, updated_at)
    VALUES (:uid, :c, NOW(), NOW())
  ");
  $insEnt->execute([
    ':uid' => $uid,
    ':c'   => ($company !== '' ? $company : null),
  ]);

  // New entrepreneur ID
  $eid = (int) $pdo->lastInsertId();

  // Finalize database changes
  $pdo->commit();

  // Log the user in immediately after signup
  login_user([
    'user_id'         => $uid,
    'name'            => $name,
    'email'           => $email,
    'role'            => 'entrepreneur',
    'entrepreneur_id' => $eid,
  ]);

  flash_set('success', 'Account created. Welcome, ' . $name . '!');

  // Redirect to home page (or dashboard)
  redirect(base_url());

} catch (Throwable $e) {
  // Undo changes if something failed
  $pdo->rollBack();
  error_log($e->getMessage());

  flash_set('error', 'Could not create account.');
  redirect(base_url('signup.php'));
}
