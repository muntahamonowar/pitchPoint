<?php

// Load core app setup: session start, database connection, functions, etc.
require __DIR__ . '/bootstrap.php';

// If a page did not set a custom title, use a default one
$TITLE = $TITLE ?? 'PitchPoint';

// Some pages may include extra CSS files, so we keep this flexible
$PAGE_CSS = $PAGE_CSS ?? [];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= h($TITLE) ?></title>

  <!-- Main global stylesheet -->
  <link rel="stylesheet" href="<?= h(root_url('style.css')) ?>">

  <!-- Additional page-specific styles if provided -->
  <?php foreach ($PAGE_CSS as $css): ?>
    <link rel="stylesheet" href="<?= h(root_url($css)) ?>">
  <?php endforeach; ?>

  <!-- Styling for active navigation menu item -->
  <style>
    .topnav a.active {
      background: var(--green, #037a5a);
      color: #fff !important;
      border-radius: 999px;
      position: relative;
    }
  </style>
</head>

<body>

  <!-- Show flash messages (success, error, etc.) -->
  <?php require __DIR__ . '/flash.php'; ?>

  <!-- Navigation bar -->
  <?php require dirname(__DIR__) . '/partials/nav.php'; ?>

  <!-- Begin main page content -->
  <main class="container">
