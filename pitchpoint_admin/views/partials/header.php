<?php /** @var string $title */ ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= esc($title ?? 'PitchPoint Admin'); ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Admin theme -->
    <link rel="stylesheet" href="assets/css/admin.css">

    <!-- Icons used in footer -->
    <link rel="stylesheet"
          href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<a href="#main" class="skip-link">Skip to main content</a>

<!-- Main admin grid: sidebar + content + footer -->
<div class="admin-layout">
