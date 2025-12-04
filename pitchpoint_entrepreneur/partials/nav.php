<?php
$current = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

function active(string $file): string
{
  global $current;
  return $current === $file ? 'active' : '';
}

$u = current_user();
?>

<nav class="topnav">

  <!-- LEFT: logo + PitchPoint -->
  <a class="brand" href="<?= h(base_url('index.php')) ?>">
    <img
      src="<?= h(base_url('logo.png')) ?>"
      alt="PitchPoint logo"
      class="brand-logo">
    <span class="brand-name">PitchPoint</span>
  </a>

  <?php if ($u): ?>

    <!-- CENTER: main menu -->
    <div class="nav-links">
      <a class="<?= active('index.php') ?>" href="<?= h(base_url('index.php')) ?>">Dashboard</a>
      <a class="<?= active('my-projects.php') ?>" href="<?= h(base_url('my-projects.php')) ?>">My Projects</a>
      <a class="<?= active('analytics.php') ?>" href="<?= h(base_url('analytics.php')) ?>">Analytics</a>
      <a class="<?= active('following.php') ?>" href="<?= h(base_url('following.php')) ?>">Following</a>
      <a class="<?= active('invite.php') ?>" href="<?= h(base_url('invite.php')) ?>">Invite</a>
      <a class="<?= active('messages.php') ?>" href="<?= h(base_url('messages.php')) ?>">Messages</a>
      <a class="<?= active('profile.php') ?>" href="<?= h(base_url('profile.php')) ?>">Profile</a>
    </div>

    <!-- RIGHT: logout -->
    <div class="right-links">
      <a class="logout-link" href="/pitchPoint/auth/logout.php">Logout</a>
    </div>

  <?php else: ?>

    <div class="nav-links"></div>

    <div class="right-links">
      <a class="<?= active('login.php') ?>" href="/pitchPoint/auth/login.php">Login</a>
      <a class="<?= active('signup.php') ?>" href="<?= h(base_url('signup.php')) ?>">Sign Up</a>
    </div>

  <?php endif; ?>

</nav>