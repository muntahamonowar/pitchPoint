<?php
// config/db.php

if (!function_exists('db')) {
    function db(): PDO {
        static $pdo = null;

        if ($pdo) return $pdo;

        $host = '127.0.0.1';
        $db   = 'pitchpoint';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $pdo = new PDO($dsn, $user, $pass, $opt);
        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }

        return $pdo;
    }
}
