<?php $current = admin_current(); ?>

<aside class="sidebar" aria-label="Admin main navigation">

    <!-- BRAND + LOGO -->
    <div class="sidebar-brand">
        <img src="assets/img/logo.png"
             alt="PitchPoint Logo"
             class="sidebar-logo">
        <div class="brand-text">
            <span class="brand-title">PitchPoint</span>
            
        </div>
    </div>

    <!-- Admin info -->
    <?php if ($current): ?>
        <p class="admin-info">
            <strong><?= esc($current['admin_name']); ?></strong><br>
            <small><?= esc($current['email']); ?></small>
        </p>
    <?php endif; ?>

    <?php $c = $_GET['c'] ?? 'dashboard'; ?>

    <nav>
        <a href="index.php?c=dashboard&a=index"
           aria-current="<?= $c === 'dashboard' ? 'page' : 'false'; ?>">
            Dashboard
        </a>

        
        <a href="index.php?c=users&a=index"
           aria-current="<?= $c === 'users' ? 'page' : 'false'; ?>">
            Users
        </a>

        <a href="index.php?c=projects&a=index"
           aria-current="<?= $c === 'projects' ? 'page' : 'false'; ?>">
            Projects
        </a>

        <a href="index.php?c=investments&a=index"
           aria-current="<?= $c === 'investments' ? 'page' : 'false'; ?>">
            Investments
        </a>

        
        <a href="index.php?c=approvals&a=index"
           aria-current="<?= $c === 'approvals' ? 'page' : 'false'; ?>">
            Approvals
        </a>

        <a href="index.php?c=notifications&a=index"
           aria-current="<?= $c === 'notifications' ? 'page' : 'false'; ?>">
            Notifications
        </a>

        <a href="index.php?c=emails&a=index"
           aria-current="<?= $c === 'emails' ? 'page' : 'false'; ?>">
            Emails
        </a>

        <a href="index.php?c=auth&a=logout">
            Logout
        </a>
    </nav>
</aside>

<!-- Main content area (row 1, right side in the grid) -->
<main id="main" class="main" tabindex="-1">
