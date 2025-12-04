<?php
// Set page title for the header
$TITLE = 'Following';

// Shared header + login required
require dirname(__DIR__) . '/includes/header.php';
require_login();

// Database connection
$pdo = db();

// Arrays to store results
$following = $followers = [];

try {
  // People the current user follows
  $q1 = $pdo->prepare(
    "SELECT u.name, u.email
     FROM follows f
     JOIN users u ON u.user_id = f.following_user_id
     WHERE f.follower_user_id = :uid
     ORDER BY u.name"
  );
  $q1->execute([':uid' => current_user()['user_id']]);
  $following = $q1->fetchAll();

  // People who follow the current user
  $q2 = $pdo->prepare(
    "SELECT u.name, u.email
     FROM follows f
     JOIN users u ON u.user_id = f.follower_user_id
     WHERE f.following_user_id = :uid
     ORDER BY u.name"
  );
  $q2->execute([':uid' => current_user()['user_id']]);
  $followers = $q2->fetchAll();

} catch (Throwable $e) {
  // On error, show empty lists but do not break page
  $following = $followers = [];
}
?>

<h1 class="page-title">Following</h1>

<div class="two-col">

  <!-- Who I follow -->
  <section>
    <h3>You follow</h3>
    <ul>
      <?php foreach ($following as $p): ?>
        <li>
          <?= h($p['name']) ?>
          <small>(<?= h($p['email']) ?>)</small>
        </li>
      <?php endforeach; ?>

      <?php if (!$following): ?>
        <li class="muted">None yet</li>
      <?php endif; ?>
    </ul>
  </section>

  <!-- Who follows me -->
  <section>
    <h3>Followers</h3>
    <ul>
      <?php foreach ($followers as $p): ?>
        <li>
          <?= h($p['name']) ?>
          <small>(<?= h($p['email']) ?>)</small>
        </li>
      <?php endforeach; ?>

      <?php if (!$followers): ?>
        <li class="muted">None yet</li>
      <?php endif; ?>
    </ul>
  </section>

</div>

<?php
// Close layout
require dirname(__DIR__) . '/includes/footer.php';
?>