<?php
/**
 * Common helper functions used across the whole project:
 * - h(): Escape output for security
 * - redirect(): Move user to another page safely
 * - base_url() / root_url(): Build project URLs correctly
 * - flash_*(): Temporary messages between requests
 * - csrf_*(): Security protection for forms
 */

declare(strict_types=1);

/**
 * Escape output before printing to the browser.
 * Prevents XSS attacks by converting special characters.
 */
function h(string $s): string
{
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a different page.
 * exit is required so no more code runs after redirect.
 */
function redirect(string $path): never
{
  header('Location: ' . $path);
  exit;
}

/**
 * Build URLs for pages inside the `/public` folder.
 * Example: base_url('index.php') → /pitchPoint/pitchpoint_entrepreneur/public/index.php
 */
function base_url(string $path = ''): string
{
  $prefix = '/pitchPoint/pitchpoint_entrepreneur/public';
  if ($path && $path[0] !== '/') {
    $path = '/' . $path;
  }
  return $prefix . $path;
}

/**
 * Build URLs for root-level files, outside /public.
 * Example: root_url('style.css') → /pitchPoint/pitchpoint_entrepreneur/style.css
 */
function root_url(string $path = ''): string
{
  $prefix = '/pitchPoint/pitchpoint_entrepreneur';
  if ($path && $path[0] !== '/') {
    $path = '/' . $path;
  }
  return $prefix . $path;
}

//
// Flash Messages
// These messages appear once after redirect and then disappear.
//

/**
 * Store a flash message in the session.
 * $type is usually: 'success', 'error', 'info'
 */
function flash_set(string $type, string $msg): void
{
  $_SESSION['flash'][$type][] = $msg;
}

/**
 * Retrieve all flash messages and remove them from the session.
 * Called in footer.php or header.php to show notifications.
 */
function flash_consume(): array
{
  $f = $_SESSION['flash'] ?? [];
  unset($_SESSION['flash']);
  return $f;
}

//
// CSRF Protection
// Helps prevent Cross-Site Request Forgery in forms.
//

/**
 * Generate and store a unique CSRF token in session.
 * Included as a hidden field in forms.
 */
function csrf_token(): string
{
  if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf'];
}

/**
 * Validate CSRF token from the form.
 * Compares it with the token stored in the session.
 */
function csrf_validate(string $t): bool
{
  return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $t);
}
