<?php

declare(strict_types=1);

// Get currently logged-in user from the session.
// Returns an array with user data or null if nobody is logged in.
// Handles both old format (user_id) and new format (id/user_id from auth system)
function current_user(): ?array
{
  $user = $_SESSION['user'] ?? null;
  if (!$user) {
    return null;
  }
  
  // Ensure user_id exists for compatibility (auth system uses 'id')
  if (!isset($user['user_id']) && isset($user['id'])) {
    $user['user_id'] = $user['id'];
  }
  
  return $user;
}

// Protect pages that require login.
// If there is no logged-in user, show a message and send them to login page.
function require_login(): void
{
  if (!current_user()) {
    flash_set('error', 'Please log in.');
    header('Location: /pitchPoint/auth/login.php');
    exit;
  }
}

// Save user data into the session after a successful login.
// This makes it easy to access user info on any page.
function login_user(array $user): void
{
  $_SESSION['user'] = [
    'user_id' => (int) $user['user_id'],
    'name' => $user['name'],
    'email' => $user['email'],
    'role' => $user['role'] ?? 'entrepreneur',
    'entrepreneur_id' => isset($user['entrepreneur_id'])
      ? (int) $user['entrepreneur_id']
      : 0,
  ];
}

// Log the user out completely.
// Clears session data, removes the session cookie, and destroys the session.
function logout_user(): void
{
  $_SESSION = [];

  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(
      session_name(),
      '',
      time() - 42000,
      $p['path'],
      $p['domain'],
      $p['secure'],
      $p['httponly']
    );
  }

  session_destroy();
}

// Get the entrepreneur_id for the logged-in user.
// For now, if it is missing, it falls back to 1 so the app keeps working.
function me_id(): int
{
  $u = current_user();
  return ($u && !empty($u['entrepreneur_id']))
    ? (int) $u['entrepreneur_id']
    : 1;
}
