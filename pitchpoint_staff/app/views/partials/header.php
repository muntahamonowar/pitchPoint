<?php
declare(strict_types=1);

$title = $title ?? 'PitchPoint';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Main CSS for the guest -->
    <link rel="stylesheet" href="/pitchpoint_staff/public/css/style.css">

    <!-- css for the staff -->
    <link rel="stylesheet" href="/pitchpoint_staff/public/css/staff.css">

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>

<body>

<header class="topbar">
    <div class="topbar-row">
        <a class="brand" href="/pitchpoint_staff/public/index.php">
           <img src="/pitchPoint/auth/logo.png" class="logo" alt="PitchPoint Logo">
            <span>PITCHPOINT</span>
        </a>
    </div>
</header>

<main class="page-container">
