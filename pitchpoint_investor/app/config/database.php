<?php // Opening PHP tag to start PHP code execution
// app/config/database.php

// --- MySQL connection settings ---
const DB_HOST = '127.0.0.1'; // Define the database host constant (localhost IP address)
const DB_PORT = '3306'; // Define the database port constant (MySQL port number)
const DB_NAME = 'pitchpoint'; // Define the database name constant
const DB_USER = 'root'; // Define the database username constant
const DB_PASS = ''; // Define the database password constant (empty for local development)

/**
 * Get a shared PDO instance
 */
if (!function_exists('db')) { // Check if the db() function already exists to avoid redeclaration
    function db(): PDO { // Define a function that returns a PDO database connection instance
        static $pdo = null; // Declare a static variable to store the PDO instance (singleton pattern)
        if ($pdo !== null) return $pdo; // If PDO instance already exists, return it immediately (singleton pattern)

        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4'; // Build the Data Source Name string for PDO connection

        $pdo = new PDO($dsn, DB_USER, DB_PASS, [ // Create a new PDO instance with connection parameters
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Set error mode to throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Set default fetch mode to return associative arrays
        ]);

        $pdo->exec('SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci'); // Execute SQL to set character set and collation for proper UTF-8 support

        return $pdo; // Return the PDO instance
    }
}

// Small HTML escape helper (same as your StudentHub)
if (!function_exists('esc')) { // Check if the esc() function already exists to avoid redeclaration
  function esc($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); } // Define a function to escape HTML special characters for security (XSS prevention)
}
