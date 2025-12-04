<?php

// Use strict types to catch type-related mistakes early
declare(strict_types=1);

// Make sure the session is started before we use $_SESSION anywhere
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Set the default timezone so all dates/times are consistent in the app
date_default_timezone_set('Europe/Copenhagen');

// Bring in the core files used across the whole project
require __DIR__ . '/db.php';        // Database connection (PDO)
require __DIR__ . '/functions.php'; // Helper functions (URLs, flash, csrf, etc.)
require __DIR__ . '/auth.php';      // Authentication helpers (login, logout, current_user)

// Turn on error reporting during development
// This makes PHP show warnings/notices so bugs are easier to spot.
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
