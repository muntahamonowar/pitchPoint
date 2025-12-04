<?php
declare(strict_types=1);

// --- MySQL connection settings ---
const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'pitchpoint';
const DB_USER = 'root';
const DB_PASS = '';

if (!function_exists('db')) {
    function db(): PDO {
        static $pdo = null;
        if ($pdo !== null) return $pdo;

        $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        return $pdo;
    }
}

if (!function_exists('esc')) {
  function esc($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}
