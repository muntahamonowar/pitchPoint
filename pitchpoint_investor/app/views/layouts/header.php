<?php
// app/views/layouts/header.php

if (!function_exists('route')) { // Check if route() function already exists to avoid redeclaration
    function route(string $path): string {
        // Always builds: /pitchPoint/pitchPoint_investor/index/investorindex.php?url=...
        return '/pitchPoint/pitchPoint_investor/index/investorindex.php?url=' . ltrim($path, '/');
    }
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$loggedIn = !empty($_SESSION['user_id']);      // Check if user is logged in
$title    = $title ?? 'PitchPoint Investor';   // Page title (fallback)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Main styles -->
    <link rel="stylesheet" href="/pitchPoint/pitchPoint_investor/CSS/style.css">
    <link rel="stylesheet" href="/pitchPoint/pitchPoint_investor/CSS/footer.css">

    <!-- Font Awesome CDN -->
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- TEMP: inline debug to PROVE CSS is working -->
    <style>
      body {
        outline: 5px solid fuchsia; /* just to see it's applied */
      }
    </style>
</head>
<body>

<header class="topbar">
    <div class="wrap topbar-inner">
        <a class="brand" href="<?= route('project/explore') ?>">
            <img src="/pitchPoint/logo.png"
                 alt="PitchPoint Logo"
                 class="logo"
                 style="width: 32px; height: 32px; border-radius: 50%;">
            PitchPoint
        </a>

        <nav class="nav">
            <a href="<?= route('project/explore') ?>">Explore</a>
            <a href="<?= route('investor/investments') ?>">Investments</a>
            <a href="<?= route('investor/profile') ?>">Profile</a>

            <?php if ($loggedIn): ?>
                <a href="<?= route('message/inbox') ?>">Inbox</a>
                <a href="<?= route('auth/logout') ?>" class="link">Logout</a>
            <?php else: ?>
                <a href="<?= route('auth/login') ?>">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="wrap">
