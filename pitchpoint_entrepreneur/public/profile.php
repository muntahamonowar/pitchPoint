<?php
// Page title displayed in the browser and header
$TITLE = 'Profile';

require dirname(__DIR__) . '/includes/header.php';
require_login(); // Only logged-in users can access profile

$pdo = db();
$user = current_user();

// Load extended entrepreneur details for this user
$ent = [];
try {
  $q = $pdo->prepare(
    "SELECT e.entrepreneur_id, e.company_name, e.website, e.location
     FROM entrepreneurs e
     WHERE e.entrepreneur_id = :eid
     LIMIT 1"
  );
  $q->execute([':eid' => me_id()]);
  $ent = $q->fetch() ?: [];
} catch (Throwable $e) {
  // If anything fails, profile still loads with defaults
  $ent = [];
}

// Locate user avatar file (if it exists in uploads/)
$avatarFile = null;
$avatarGlob = glob(dirname(__DIR__) . '/uploads/avatar_' . (int) ($user['user_id'] ?? 0) . '.*');
if ($avatarGlob && is_file($avatarGlob[0])) {
  $avatarFile = basename($avatarGlob[0]);
}
// Build URL for <img>, or null if absent
$avatarUrl = $avatarFile ? base_url('../uploads/' . $avatarFile) : null;
?>

<h1 class="page-title">Profile</h1>

<div class="profile-layout">

  <!-- Display user information -->
  <div class="profile-card">

    <!-- Avatar or fallback letter -->
    <div class="profile-avatar">
      <?php if ($avatarUrl): ?>
        <img src="<?= h($avatarUrl) ?>" alt="Profile photo">
      <?php else: ?>
        <div class="avatar-placeholder">
          <?= h(strtoupper(substr($user['name'] ?? 'U', 0, 1))) ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Basic details -->
    <p><strong>Name:</strong> <?= h($user['name'] ?? '') ?></p>
    <p><strong>Email:</strong> <?= h($user['email'] ?? '') ?></p>
    <p><strong>Company:</strong> <?= h($ent['company_name'] ?? '—') ?></p>
    <p><strong>Website:</strong> <?= h($ent['website'] ?? '—') ?></p>
    <p><strong>Location:</strong> <?= h($ent['location'] ?? '—') ?></p>
  </div>

  <!-- Update fields form -->
  <div class="profile-edit">
    <h2>Edit Profile</h2>

    <form class="form" action="<?= h(base_url('../actions/update_profile.php')) ?>" method="post"
      enctype="multipart/form-data">

      <!-- CSRF security token -->
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">

      <!-- Editable personal + business fields -->
      <label>Name
        <input type="text" name="name" required value="<?= h($user['name'] ?? '') ?>">
      </label>

      <label>Email
        <input type="email" name="email" required value="<?= h($user['email'] ?? '') ?>">
      </label>

      <label>Company
        <input type="text" name="company_name" value="<?= h($ent['company_name'] ?? '') ?>">
      </label>

      <label>Website
        <input type="url" name="website" value="<?= h($ent['website'] ?? '') ?>" placeholder="https://example.com">
      </label>

      <label>Location
        <input type="text" name="location" value="<?= h($ent['location'] ?? '') ?>" placeholder="City, Country">
      </label>

      <!-- Upload new avatar file -->
      <label>Profile Photo (optional)
        <input type="file" name="profile_photo" accept="image/*">
        <small class="muted">
          A new upload will replace the current photo.
        </small>
      </label>

      <div class="actions">
        <button class="btn btn-primary" type="submit">Save Changes</button>
      </div>

    </form>
  </div>

</div>

<?php
// Close layout
require dirname(__DIR__) . '/includes/footer.php';
?>