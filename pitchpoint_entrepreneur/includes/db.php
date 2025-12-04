<?php

// Strict type checking for safer code
declare(strict_types=1);

// Database connection settings used throughout the app
const DB_HOST = '127.0.0.1';
const DB_NAME = 'pitchpoint';
const DB_USER = 'root';   // Default XAMPP username
const DB_PASS = '';       // Default XAMPP password (empty)
const DB_CHARSET = 'utf8mb4'; // Supports emojis + all characters

/**
 * Create and return a PDO database connection.
 * Uses a static variable so the connection is made only once.
 * (Better performance than connecting on every query.)
 */
function db(): PDO
{
  static $pdo = null;

  // If a connection already exists, return it
  if ($pdo instanceof PDO) {
    return $pdo;
  }

  // Build the DSN (Database Source Name)
  $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

  // PDO configuration options for safer and cleaner SQL handling
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions for SQL errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays
    PDO::ATTR_EMULATE_PREPARES => false,                  // Use real prepared statements
  ];

  // Create the connection
  $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

  return $pdo;
}
